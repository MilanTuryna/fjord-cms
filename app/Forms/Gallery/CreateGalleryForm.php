<?php


namespace App\Forms\Gallery;


use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Forms\Gallery\Data\GalleryFormData;
use App\Model\Database\Repository\Gallery\Entity\Gallery;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\FileSystem\Gallery\GalleryDataProvider;
use App\Model\FileSystem\Gallery\GalleryUploadManager;
use Exception;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Utils\DateTime;

/**
 * Class CreateGalleryForm
 * @package App\Forms\Gallery
 */
class CreateGalleryForm extends GalleryForm
{
    /**
     * GalleryForm constructor.
     * @param Presenter $presenter
     * @param GalleryRepository $galleryRepository
     * @param ItemsRepository $itemsRepository
     * @param GalleryDataProvider $galleryDataProvider
     * @param int $admin_id
     * @param FormRedirect $formRedirect
     */
    public function __construct(protected Presenter $presenter, private GalleryRepository $galleryRepository, private ItemsRepository $itemsRepository, private GalleryDataProvider $galleryDataProvider, private int $admin_id, private FormRedirect $formRedirect)
    {
        parent::__construct($this->presenter, $this->galleryRepository, $this->itemsRepository, $this->admin_id);
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     * @throws Exception
     */
    public function success(Form $form, GalleryFormData &$data) {
        parent::success($form, $data);
        $data->admin_id = $this->admin_id;
        $data->created = new DateTime();
        $success = $this->successTemplate($form, $data->iterable(true), new FormMessage("Galerie byla úspěšně vytvořena", "Galerie nemohla být z neznámého důvodu vytvořena."), $this->formRedirect);
        if($success) {
            $fetch = $this->galleryRepository->findByColumn(Gallery::uri, $data->uri)->fetch();
            $galleryUploadManager = new GalleryUploadManager($this->galleryDataProvider, $fetch->id);
            $this->uploadImages($form, $data, $galleryUploadManager);
        }
    }
}