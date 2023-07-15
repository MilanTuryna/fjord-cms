<?php


namespace App\Forms\Gallery;


use App\Forms\FlashMessages;
use App\Forms\Form;
use App\Forms\FormOption;
use App\Forms\Gallery\Data\GalleryFormData;
use App\Forms\Gallery\Data\ItemFormData;
use App\Forms\RepositoryForm;
use App\Model\Database\Columns\URI;
use App\Model\Database\Repository\Gallery\Entity\GalleryItem;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\DI\FFMpegProvider;
use App\Model\FileSystem\FileUtils;
use App\Model\FileSystem\Gallery\GalleryUploadManager;
use App\Model\FileSystem\Gallery\Objects\GalleryItemFile;
use App\Model\UI\FlashMessages\AddedGalleryItems;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use JetBrains\PhpStorm\Pure;
use Nette\Application\UI\Presenter;
use Nette\Forms\Container;
use Nette\Http\FileUpload;
use Nette\Utils\DateTime;

/**
 * Class GalleryForm
 * @package App\Forms\Gallery
 */
class GalleryForm extends RepositoryForm
{
    /**
     * GalleryForm constructor.
     * @param Presenter $presenter
     * @param GalleryRepository $galleryRepository
     * @param ItemsRepository $itemsRepository
     * @param int $admin_id
     */
    public function __construct(protected Presenter $presenter, private GalleryRepository $galleryRepository, private ItemsRepository $itemsRepository, private int $admin_id, private FFMpegProvider $FFMpegProvider)
    {
        parent::__construct($this->presenter, $this->galleryRepository);
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $form->addText("name", "Název galerie")->setRequired(true);
        $form->addText("description", "Popis")->setRequired(false);
        $form->addText("miniature_url", "URL adresa k miniatuře")->setOption(FormOption::OPTION_NOTE, "Můžete použít souvislý odkaz z této galerie.")->setRequired(false);
        $form->addCheckbox("private", "Má být galerie skrytá?");
        $form->addMultiUpload("_global_upload", "Hromadné nahrání obrázků/videí")->setRequired(false)->setOption(FormOption::OPTION_NOTE, "Povolené formáty: " . implode(", ", GalleryUploadManager::ALLOWED_EXTENSIONS));
        $form->addSubmit("submit", "Vytvořit galerii");

        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     * @param GalleryFormData $data
     */
    protected function success(\Nette\Application\UI\Form $form, GalleryFormData &$data) {
        $data->uri = new URI($data->name);
        $data->private = $data->private ?? false;
        $data->edited = new DateTime();
    }

    /**
     * @param \Nette\Application\UI\Form $form
     * @param GalleryFormData $data
     * @param GalleryUploadManager $galleryUploadManager
     * @throws Exception
     */
    protected function uploadImages(\Nette\Application\UI\Form $form, GalleryFormData $data, GalleryUploadManager $galleryUploadManager): void {
        $errorMessage = function (int $key) {
            return "Obrázek č. " . $key+1 . " nebyl z neznámého důvodu nahrán.";
        };
        $validItems = []; // compressed_file []
        foreach ($data->_global_upload as $i => $item) {
            if($item instanceof FileUpload) {
                $itemUpload = $item;
                $item = new ItemFormData();
            } else {
                $itemUpload = $item->file_upload;
            }
            $item->original_file = $itemUpload->getUntrustedName();
            $item->size_bytes = $itemUpload->getSize();
            $item->admin_id = $this->admin_id;
            $item->gallery_id = $galleryUploadManager->getDirectoryName();
            $item->created = new DateTime();
            if($itemUpload->isOk() && $itemUpload->isImage()) {
                $item->compressed_file = ItemFormData::encodeName($itemUpload->getUntrustedName(), $itemUpload->getImageFileExtension());
                FileUtils::checkExif($itemUpload->getTemporaryFile());
                $imageResolution = $itemUpload->getImageSize();
                $item->resolution_x = $imageResolution[0];
                $item->resolution_y = $imageResolution[1];
                $galleryUploadManager->add($itemUpload, $item->compressed_file);
                if($this->itemsRepository->insert($item->iterable(true))) {
                    $validItems[] = $item->compressed_file;
                    $iExpression = $i + 1;
                    $this->presenter->flashMessage("Obrázek č. " . $iExpression .  " (" . $item->original_file . ") byl úspěšně nahrán!", FlashMessages::SUCCESS);
                } else {
                    $form->addError($errorMessage($i));
                }
            } elseif($itemUpload->isOk() && GalleryUploadManager::isVideo($itemUpload->getUntrustedName())) {
                $item->compressed_file = ItemFormData::encodeName($itemUpload->getUntrustedName(), FileUtils::getExtension($itemUpload->getUntrustedName()));

                $tempFile = $itemUpload->getTemporaryFile();
                $ffmpeg = $this->FFMpegProvider->getInstance();
                $ffprobe =  $ffmpeg->getFFProbe();
                $dimensions = $ffprobe->streams($tempFile)->videos()->first()->getDimensions();

                $item->resolution_x = $dimensions->getWidth();
                $item->resolution_y = $dimensions->getHeight();

                $video = $ffmpeg->open($itemUpload->getTemporaryFile());
                $frame = $video->frame(TimeCode::fromSeconds(2));
                $frame->save($galleryUploadManager->getVideoFrame($item->compressed_file));
                $galleryUploadManager->add($itemUpload, $item->compressed_file);
                if($this->itemsRepository->insert($item->iterable(true))) {
                    $validItems[] = $item->compressed_file;
                    $iExpression = $i + 1;
                    $this->presenter->flashMessage("Video č. " . $iExpression .  " (" . $item->original_file . ") bylo úspěšně nahráno!", FlashMessages::SUCCESS);
                } else {
                    $form->addError($errorMessage($i));
                }
            } else {
                bdump(FileUtils::getExtension($itemUpload->getUntrustedName()));
                bdump($galleryUploadManager::ALLOWED_EXTENSIONS);
                if(!in_array(FileUtils::getExtension($itemUpload->getUntrustedName()), $galleryUploadManager::ALLOWED_EXTENSIONS)) {
                    $this->presenter->flashMessage($errorMessage($i) . sprintf(" Špatný formát souboru (povolené jsou: %s).", implode(", ",GalleryUploadManager::ALLOWED_EXTENSIONS)), FlashMessages::ERROR);
                } else {
                    $form->addError($errorMessage($i));
                }
            }
        }
        $this->presenter->flashMessage(new AddedGalleryItems($validItems));
    }
}