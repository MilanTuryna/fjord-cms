<?php


namespace App\Forms\Gallery;


use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Forms\Gallery\Data\GalleryFormData;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;

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
     * @param int $admin_id
     * @param FormRedirect $formRedirect
     */
    #[Pure] public function __construct(protected Presenter $presenter, private GalleryRepository $galleryRepository, private ItemsRepository $itemsRepository, private int $admin_id, private FormRedirect $formRedirect)
    {
        parent::__construct($this->presenter, $this->galleryRepository, $$this->itemsRepository, $this->admin_id,);
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     */
    public function success(Form $form, GalleryFormData $data) {
        $success = $this->successTemplate($form, $data, new FormMessage("Galerie byla úspěšně vytvořena", "Galerie nemohla být z neznámého důvodu vytvořena."), $this->formRedirect, null, null ,"");
        if($success) {
            $this->uploadImages($form, $data);
        }
    }
}