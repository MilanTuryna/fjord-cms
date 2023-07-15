<?php


namespace App\Presenters\Admin\Product;


use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Forms\Product\CreateProductForm;
use App\Forms\Product\EditProductForm;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Product\ProductRepository;
use App\Model\Extensions\FormMultiplier\Multiplier;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

/**
 * Class MainPresenter
 * @package App\Presenters\Admin\Product
 */
class MainPresenter extends AdminBasePresenter
{
    /**
     * MainPresenter constructor.
     * @param AdminAuthenticator $adminAuthenticator
     * @param ProductRepository $productRepository
     * @param GalleryRepository $galleryRepository
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, private ProductRepository $productRepository, private GalleryRepository $galleryRepository, string $permissionNode = AdminPermissions::PRODUCTS)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    public function renderList() {
        $this->template->products = $this->productRepository->findAll()->order("priority DESC, created DESC")->fetchAll();
    }

    public function renderNew() {
        $this->template->activeWysiwyg = true;
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function renderRemove(int $id) {
        $this->prepareActionRemove($this->productRepository, $id, new FormMessage("Produkt byl úspěšně odstraněn.", "Produkt nemohl být z neznámého důvodu odstraněn."), new FormRedirect("list"));
    }

    public function renderView(int $id) {
        $this->template->product = $this->productRepository->findById($id);
        $this->template->activeWysiwyg = true;
    }

    /**
     * @return Form
     */
    public function createComponentCreateProductForm(): Form {
        return (new CreateProductForm($this, $this->productRepository, $this->galleryRepository, new FormRedirect("view", [FormRedirect::LAST_INSERT_ID])))->create();
    }

    /**
     * @return \Nette\Application\UI\Multiplier
     */
    public function createComponentEditProductForm(): \Nette\Application\UI\Multiplier {
        return new \Nette\Application\UI\Multiplier(function ($id) {
            return (new EditProductForm($this, $this->productRepository, $this->galleryRepository, (int)$id,
                new FormRedirect(":Admin:Product:Main:remove", [$id])))->create();
        });
    }
}