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
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;

class EditGalleryForm extends GalleryForm
{
    private array $activeRowArr;

    public function __construct(protected Presenter $presenter, protected GalleryRepository $galleryRepository, protected ItemsRepository $itemsRepository, private GalleryDataProvider $galleryDataProvider, protected int $admin_id, private int $gallery_id)
    {
        parent::__construct($this->presenter, $this->galleryRepository, $this->itemsRepository, $this->admin_id);

        $this->activeRowArr = $this->galleryRepository->findById($this->gallery_id)->toArray();
    }

    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
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