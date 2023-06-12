<?php


namespace App\Forms\Gallery;


use App\Forms\FlashMessages;
use App\Forms\Form;
use App\Forms\FormOption;
use App\Forms\Gallery\Data\GalleryFormData;
use App\Forms\Gallery\Data\ItemFormData;
use App\Forms\RepositoryForm;
use App\Model\Database\Columns\URI;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\FileSystem\GalleryUploadManager;
use Exception;
use JetBrains\PhpStorm\Pure;
use Nette\Application\UI\Presenter;
use Nette\Forms\Container;
use Nette\Http\FileUpload;

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
        $form->addText("description", "Popis")->setRequired(false)->setOption(FormOption::BOTTOM_LINE, 1);
        $items = $form->addMultiplier("_items", function (Container $container, \Nette\Application\UI\Form $form) {
            $container->addUpload("file_upload", "Nahrát obrázek")->addRule(\Nette\Forms\Form::Image)
                ->addRule(\Nette\Forms\Form::MAX_FILE_SIZE, sprintf("Tento obrázek je moc velký. Nahrajte prosím jeho menší alternativu (<%smb). ", ItemFormData::MAX_FILE_SIZE), ItemFormData::MAX_FILE_SIZE_MB)->setOption(FormOption::MULTIPLIER_PARENT, "_items");;
            $container->addText("alt", "Alternativní text")
                ->setOption(FormOption::OPTION_NOTE, "Text stručně popisujicí obrázek v případě nenačtení obrázku")
                ->setHtmlAttribute("placeholder", "Velký létajicí drak")->setRequired(false)->setOption(FormOption::MULTIPLIER_PARENT, "_items");;
            $container->addText("image_description", "Popis obrázku")->setOption(FormOption::MULTIPLIER_PARENT, "_items")->setOption(FormOption::OPTION_NOTE, "(vlastní poznámka)");
        });
        $items->addRemoveButton("Smazat položku");
        $items->setOption(FormOption::FULL_WIDTH, 1);
        $items->addCreateButton("Přidat položku", 0)->addClass('btn btn-dark w-100');
        $items->setCaption("Nahrávání fotografií");
        $form->addMultiUpload("_global_upload", "Hromadné nahrání obrázků")->setRequired(false)->setOption(FormOption::UPPER_LINE, 1);
        $form->addSubmit("submit", "Vytvořit galerii");

        return $form;
    }

    /**
     * @param \Nette\Application\UI\Form $form
     * @param GalleryFormData $data
     */
    protected function success(\Nette\Application\UI\Form $form, GalleryFormData &$data) {
        $data->uri = new URI($data->name);
    }

    /**
     * @param \Nette\Application\UI\Form $form
     * @param GalleryFormData $data
     * @param GalleryUploadManager $galleryUploadManager
     * @throws Exception
     */
    protected function uploadImages(\Nette\Application\UI\Form $form, GalleryFormData $data, GalleryUploadManager $galleryUploadManager): void {
        $errorMessage = function (string $alt, int $key) {
            $altExpression = $alt ?? "neuvedeno";
            return "Obrázek č. " . $key+1 . `({$altExpression})` . "nebyl z neznámého důvodu nahrán.";
        };
        foreach (array_merge($data->_items, $data->_global_upload) as $i => $item) {
            if($item instanceof FileUpload) {
                $itemUpload = $item;
                $item = new ItemFormData();
            } else {
                $itemUpload = $item->file_upload;
            }
            if($itemUpload->isOk() && $itemUpload->isImage()) {
                $item->original_file = $itemUpload->getUntrustedName();
                $item->compressed_file = ItemFormData::encodeName($itemUpload->getUntrustedName(), $itemUpload->getImageFileExtension());
                $item->admin_id = $this->admin_id;
                $galleryUploadManager->add($itemUpload, $item->compressed_file);
                if($this->itemsRepository->insert($item->iterable(true))) {
                    $iExpression = $i + 1;
                    $this->presenter->flashMessage(`Obrázek č. {$iExpression} byl úspěšně nahrán!`, FlashMessages::SUCCESS);
                } else {
                    $form->addError($errorMessage($item->alt, $i));
                }
            } else {
                $form->addError($errorMessage($item->alt, $i));
            }
        }
    }
}