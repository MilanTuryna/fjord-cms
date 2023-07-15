<?php


namespace App\Forms\Gallery;


use App\Forms\FormMessage;
use App\Forms\FormOption;
use App\Forms\FormRedirect;
use App\Forms\Gallery\Data\GalleryFormData;
use App\Model\Database\Repository\Gallery\Entity\Gallery;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\DI\FFMpegProvider;
use App\Model\FileSystem\Gallery\GalleryDataProvider;
use App\Model\FileSystem\Gallery\GalleryUploadManager;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;

class EditGalleryForm extends GalleryForm
{
    private array $activeRowArr;

    /**
     * EditGalleryForm constructor.
     * @param Presenter $presenter
     * @param GalleryRepository $galleryRepository
     * @param ItemsRepository $itemsRepository
     * @param GalleryDataProvider $galleryDataProvider
     * @param int $admin_id
     * @param int $gallery_id
     * @param FormRedirect $deleteRoute
     * @param FFMpegProvider $FFMpegProvider
     */
    public function __construct(protected Presenter $presenter, protected GalleryRepository $galleryRepository, protected ItemsRepository $itemsRepository, private GalleryDataProvider $galleryDataProvider, protected int $admin_id, private int $gallery_id, private FormRedirect $deleteRoute, private FFMpegProvider $FFMpegProvider)
    {
        parent::__construct($this->presenter, $this->galleryRepository, $this->itemsRepository, $this->admin_id, $this->FFMpegProvider);

        $this->activeRowArr = $this->galleryRepository->findById($this->gallery_id)->toArray();
    }

    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $form['submit']->setOption(FormOption::DELETE_LINK, $this->deleteRoute);
        return self::createEditForm($form,  $this->activeRowArr);
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     * @throws Exception
     */
    #[NoReturn] public function success(Form $form, GalleryFormData &$data) {
        parent::success($form, $data);
        $this->successTemplate($form, $data->iterable(true), new FormMessage("Galerie byla úspěšně aktualizována", "Galerie nemohla být z neznámého důvodu aktualizována."), null, $this->gallery_id);
        $fetch = $this->galleryRepository->findByColumn(Gallery::uri, $data->uri)->fetch();
        $galleryUploadManager = new GalleryUploadManager($this->galleryDataProvider,$fetch->id);
        $this->uploadImages($form, $data, $galleryUploadManager);
        $this->presenter->redirect("this");
    }
}