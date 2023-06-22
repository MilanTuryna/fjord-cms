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
use App\Model\FileSystem\Gallery\GalleryUploadManager;
use App\Model\FileSystem\Gallery\Objects\GalleryItemFile;
use Exception;
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
    public function __construct(protected Presenter $presenter, private GalleryRepository $galleryRepository, private ItemsRepository $itemsRepository, private int $admin_id)
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
        $form->addMultiUpload("_global_upload", "Hromadné nahrání obrázků")->setRequired(false);
        $form->addSubmit("submit", "Vytvořit galerii");

        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     * @param GalleryFormData $data
     */
    protected function success(\Nette\Application\UI\Form $form, GalleryFormData &$data) {
        $data->uri = new URI($data->name);
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
        foreach ($data->_global_upload as $i => $item) {
            if($item instanceof FileUpload) {
                $itemUpload = $item;
                $item = new ItemFormData();
            } else {
                $itemUpload = $item->file_upload;
            }
            if($itemUpload->isOk() && $itemUpload->isImage()) {
                $item->original_file = $itemUpload->getUntrustedName();
                $item->compressed_file = ItemFormData::encodeName($itemUpload->getUntrustedName(), $itemUpload->getImageFileExtension());
                $item->size_bytes = $itemUpload->getSize();
                $item->admin_id = $this->admin_id;
                $imageResolution = $itemUpload->getImageSize();
                $item->resolution_x = $imageResolution[0];
                $item->resolution_y = $imageResolution[1];
                $item->gallery_id = $galleryUploadManager->getDirectoryName();
                $galleryUploadManager->add($itemUpload, $item->compressed_file);
                if($this->itemsRepository->insert($item->iterable(true))) {
                    $iExpression = $i + 1;
                    $this->presenter->flashMessage("Obrázek č. " . $iExpression .  " byl úspěšně nahrán!", FlashMessages::SUCCESS);
                } else {
                    $form->addError($errorMessage($i));
                }
            } else {
                if(!$itemUpload->isImage()) {
                    $form->addError($errorMessage($i) . sprintf("Špatný formát souboru (povolené jsou: %s).", implode(", ",GalleryUploadManager::ALLOWED_EXTENSIONS)));
                } else {
                    $form->addError($errorMessage($i));
                }
            }
        }
    }
}