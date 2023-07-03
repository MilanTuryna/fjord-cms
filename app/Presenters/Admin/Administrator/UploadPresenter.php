<?php


namespace App\Presenters\Admin\Administrator;


use App\Forms\FlashMessages;
use App\Forms\Upload\UploadFileForm;
use App\Model\Admin\Permissions\Specific\AdminPermissions;
use App\Model\Admin\Permissions\Utils;
use App\Model\FileSystem\Admin\AdminUploadDataProvider;
use App\Model\FileSystem\Admin\AdminUploadManager;
use App\Model\Security\Auth\AdminAuthenticator;
use App\Presenters\AdminBasePresenter;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\FileNotFoundException;

/**
 * Class UploadPresenter
 * @package App\Presenters\Admin\Administrator
 */
class UploadPresenter extends AdminBasePresenter
{
    private AdminUploadManager $adminUploadManager;

    /**
     * UploadPresenter constructor.
     * @param AdminAuthenticator $adminAuthenticator
     * @param AdminUploadDataProvider $dataProvider
     * @param string $permissionNode
     */
    public function __construct(AdminAuthenticator $adminAuthenticator, private AdminUploadDataProvider $dataProvider, string $permissionNode = AdminPermissions::UPLOAD)
    {
        parent::__construct($adminAuthenticator, $permissionNode);

        $this->adminUploadManager = new AdminUploadManager($this->dataProvider);
    }

    public function renderList() {
        $this->template->files = $this->adminUploadManager->getURLUploads();
    }

    /**
     * @throws AbortException
     */
    #[NoReturn] public function actionRemove(string $fileName): void {
        $replaceDot = str_replace("----", ".", $fileName);
        try {
            $this->adminUploadManager->deleteUpload($replaceDot);
        } catch (FileNotFoundException $fileNotFoundException) {

        }
        $this->flashMessage("Daný soubor byl úspěšně odstraněn.", FlashMessages::SUCCESS);
        $this->redirect("list");
    }

    /**
     * @return Form
     */
    public function createComponentUploadFileForm(): Form {
        return (new UploadFileForm($this, $this->adminUploadManager))->create();
    }
}