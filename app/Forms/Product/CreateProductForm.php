<?php


namespace App\Forms\Product;


use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Forms\Product\Data\ProductFormData;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Product\ProductRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Utils\DateTime;

class CreateProductForm extends ProductForm
{

    public function __construct(Presenter $presenter, ProductRepository $repository, GalleryRepository $galleryRepository, private FormRedirect $formRedirect)
    {
        parent::__construct($presenter, $repository, $galleryRepository);
        $this->galleryRepository = $galleryRepository;
    }

    /**
     * @param Form $form
     * @param ProductFormData $data
     * @throws AbortException
     * @throws InvalidLinkException
     */
    public function success(Form $form, ProductFormData &$data): void
    {
        parent::success($form, $data);
        $data->created = new DateTime();

        $this->successTemplate($form, $data->iterable(), new FormMessage("Nabídka byla úspěšně vytvořena.", "Nabídka nebyla z neznámého důvodu vytvořena."), $this->formRedirect);
    }
}