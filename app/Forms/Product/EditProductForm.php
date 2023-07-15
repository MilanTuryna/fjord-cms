<?php


namespace App\Forms\Product;


use App\Forms\FormMessage;
use App\Forms\FormOption;
use App\Forms\FormRedirect;
use App\Forms\Product\Data\ProductFormData;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Product\ProductRepository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;

class EditProductForm extends ProductForm
{
    #[Pure] public function __construct(Presenter $presenter, ProductRepository $repository, GalleryRepository $galleryRepository, private int $productId, private FormRedirect $deleteHref)
    {
        parent::__construct($presenter, $repository, $galleryRepository);
    }

    /**
     * @return Form
     */
    public function create(): Form
    {
        $form = parent::create();
        $product = $this->repository->findById($this->productId);
        $form["submit"]->setOption(FormOption::DELETE_LINK, $this->deleteHref);
        return $this::createEditForm($form, $product);
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     */
    public function success(Form $form, ProductFormData &$data): void
    {
        parent::success($form, $data);

        $this->successTemplate($form, $data->iterable(),
            new FormMessage("Produkt byl úspěšně aktualizován",
                "Produkt nebyl z neznámého důvodu aktualizován."), new FormRedirect("this"), $this->productId);
    }
}