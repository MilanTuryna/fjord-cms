<?php


namespace App\Presenters\Admin\Gallery;


use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Database\Repository\Gallery\Entity\GalleryItem;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;

/**
 * Class MainPresenter
 * @package App\Presenters\Admin\Gallery
 */
class MainPresenter extends AdminBasePresenter
{
    /**
     * MainPresenter constructor.
     * @param AdminAuthenticator $adminAuthenticator
     * @param ItemsRepository $itemsRepository
     * @param GalleryRepository $galleryRepository
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, protected ItemsRepository $itemsRepository, protected GalleryRepository $galleryRepository, string $permissionNode = AdminPermissions::GALLERY_EDIT)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    public function renderOverview() {
    }

    /**
     * @param int $galleryId
     */
    public function renderView(int $galleryId) {
        $this->template->gallery = $this->galleryRepository->findById($galleryId);
        $this->template->items = $this->itemsRepository->findByColumn(GalleryItem::gallery_id, $galleryId);
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionRemove(int $galleryId) {
        $this->prepareActionRemove($this->itemsRepository, $galleryId, new FormMessage("Galerie byla úspěšně odstraněna i se všemi fotky.", "Galerie nebyla z neznámého důvodu odstraněna."), "list");
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionRemoveImage(int $galleryId, int $imageId) {
        //$galleryId only for SEO purposes and backlink
        $this->prepareActionRemove($this->itemsRepository, $imageId, new FormMessage("Obrázek byl ze zadané galerie úspěšně odstraněn", "Obrázek nemohl být z neznámého důvodu odstraněn."), new FormRedirect(":Admin:Gallery:Main:view", [$galleryId]));
    }
}