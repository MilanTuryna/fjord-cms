<?php


namespace App\Presenters\Admin\Gallery;


use App\Forms\FlashMessages;
use App\Forms\FormMessage;
use App\Forms\FormRedirect;
use App\Forms\Gallery\CreateGalleryForm;
use App\Forms\Gallery\Data\ItemFormData;
use App\Forms\Gallery\EditGalleryForm;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Database\Repository\Gallery\Entity\Gallery;
use App\Model\Database\Repository\Gallery\Entity\GalleryItem;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\DI\FFMpegProvider;
use App\Model\FileSystem\Gallery\Exceptions\ImageNotExistException;
use App\Model\FileSystem\Gallery\GalleryDataProvider;
use App\Model\FileSystem\Gallery\GalleryFacadeFactory;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use App\Utils\ArrayUtils;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\FileNotFoundException;
use ReflectionException;

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
     * @param GalleryDataProvider $galleryDataProvider
     * @param GalleryFacadeFactory $galleryFacadeFactory
     * @param FFMpegProvider $FFMpegProvider
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, protected ItemsRepository $itemsRepository, protected GalleryRepository $galleryRepository, protected GalleryDataProvider $galleryDataProvider, protected GalleryFacadeFactory $galleryFacadeFactory, protected FFMpegProvider $FFMpegProvider,
                                string $permissionNode = AdminPermissions::GALLERY_EDIT)
    {
        parent::__construct($adminAuthenticator, $permissionNode);
    }

    /**
     * @throws ReflectionException
     */
    public function renderOverview() {
        $galleries = $this->galleryRepository->findAll()->fetchAll();
        $lastItems = [];
        /**
         * @var $item GalleryItem
         */
        foreach ($galleries as $gallery) {
             $lastItems[$gallery->id] = $this->galleryFacadeFactory->getGalleryFacade($gallery->id)->getItems();
        }
        $this->template->lastItems = $lastItems;
        $this->template->galleries = $galleries;
    }

    /**
     * @param int $galleryId
     * @throws ReflectionException
     */
    public function renderView(int $galleryId) {
        $this->template->gallery = $this->galleryRepository->findById($galleryId);
        $facade = $this->galleryFacadeFactory->getGalleryFacade($galleryId);
        $items = $this->template->items = $facade->getItems();
        $this->template->galleryFileInfo = $facade->getGalleryFileInfo();
    }

    /**
     * @throws ReflectionException
     * @throws ImageNotExistException
     */
    public function renderViewImage(int $galleryId, int $imageId) {
        $gallery = $this->template->gallery = $this->galleryRepository->findById($galleryId);
        $galleryFacade = $this->galleryFacadeFactory->getGalleryFacade($galleryId);
        $this->template->item = $galleryFacade->getGalleryItemFile($imageId);
        $this->template->administrators = $this->accountRepository->findAll()->fetchPairs("id");
    }

    /**
     * @param int $galleryId
     */
    private function removeImages(int $galleryId): void {
        $this->itemsRepository->findByColumn(GalleryItem::gallery_id, $galleryId)->delete();
        $this->galleryFacadeFactory->getGalleryFacade($galleryId)->getGalleryUploadManager()->deleteUploads(true);
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionRemove(int $galleryId) {
        $this->removeImages($galleryId);
        $this->prepareActionRemove($this->galleryRepository, $galleryId, new FormMessage("Galerie byla úspěšně odstraněna i se všemi fotky.", "Galerie nebyla z neznámého důvodu odstraněna."),
            "overview");
    }

    /**
     * @param int $galleryId
     * @param int $imageId
     * @return void
     * @throws AbortException
     */
    #[NoReturn] public function actionRemoveImage(int $galleryId, int $imageId): void
    {
        /**
         * @var $item ItemFormData
         */
        $facade = $this->galleryFacadeFactory->getGalleryFacade($galleryId);
        /**
         * @var $image GalleryItem
         */
        $image = $this->itemsRepository->findById($imageId);
        if($image) {
            $this->itemsRepository->deleteById($imageId);
            $galleryUploadManager = $facade->getGalleryUploadManager();
            $galleryUploadManager->deleteUpload($image->compressed_file);
            if($galleryUploadManager::isVideo($image->compressed_file)) $galleryUploadManager->removeVideoFrame($image->compressed_file);
            $this->flashMessage("Daný obrázek byl odstraněn úspěšně.", FlashMessages::SUCCESS);
        } else {
            $this->flashMessage("Daný obrázek nebyl odstraněn, protože neexistuje. Někde možná nastala chyba.", FlashMessages::ERROR);
        }
        $this->redirect("view", $galleryId);
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionRemoveImages(int $galleryId): void {
        $this->removeImages($galleryId);
        $this->flashMessage("Všechny obrázky v galerii byly úspěšně odstraněny.", FlashMessages::SUCCESS);
        $this->redirect("view", $galleryId);
    }



    /**
     * @return Form
     */
    public function createComponentCreateGalleryForm(): Form {
        return (new CreateGalleryForm($this, $this->galleryRepository, $this->itemsRepository, $this->galleryDataProvider, $this->admin->id, new FormRedirect("view", [FormRedirect::LAST_INSERT_ID]), $this->FFMpegProvider))->create();
    }

    public function createComponentEditGalleryForm(): Multiplier {
        return new Multiplier(function ($id) {
            return (new EditGalleryForm($this, $this->galleryRepository, $this->itemsRepository, $this->galleryDataProvider, $this->admin->id, (int)$id, new FormRedirect("remove", [$id]), $this->FFMpegProvider))->create();
        });
    }
}