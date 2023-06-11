<?php


namespace App\Forms\Gallery;


use App\Forms\FormMessage;
use App\Forms\Gallery\Data\GalleryFormData;
use App\Model\Database\Repository\Gallery\Entity\Gallery;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\FileSystem\GalleryUploadManager;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;

class EditGalleryForm extends GalleryForm
{
    public ?ActiveRow $activeRow;

    public function __construct(protected Presenter $presenter, protected GalleryRepository $galleryRepository, protected ItemsRepository $itemsRepository, protected int $admin_id, private int $gallery_id)
    {
        parent::__construct($this->presenter, $this->galleryRepository, $this->itemsRepository, $this->admin_id);

        $this->activeRow = $this->galleryRepository->findById($this->gallery_id);
    }

    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        return self::createEditForm($form, $this->activeRow);
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     * @throws \Exception
     */
    public function success(Form $form, GalleryFormData &$data) {
        parent::success($form, $data);
        $success = $this->successTemplate($form, $data->iterable(true), new FormMessage("Galerie byla úspěšně vytvořena", "Galerie nemohla být z neznámého důvodu vytvořena."));
        if($success) {
            $galleryUploadManager = new GalleryUploadManager($this->galleryRepository->findByColumn(Gallery::uri, $data->uri)->fetch()->id);
            $this->uploadImages($form, $data, $galleryUploadManager);
        }
    }
}