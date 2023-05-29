<?php

namespace App\Forms\Gallery\Item;

use App\Forms\Form;
use App\Forms\FormMessage;
use App\Forms\Gallery\Data\EditItemFormData;
use App\Forms\RepositoryForm;
use App\Model\Database\Repository\Gallery\Entity\Gallery;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use App\Model\FileSystem\GalleryUploadManager;
use Nette\Application\AbortException;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;

class EditItemForm extends RepositoryForm
{
    private ActiveRow $activeRow;

    public function __construct(Presenter $presenter, ItemsRepository $itemsRepository, private GalleryUploadManager $galleryUploadManager, private int $galleryId, private int $itemId)
    {
        parent::__construct($presenter, $itemsRepository);

        $this->presenter = $presenter;
        $this->activeRow = $itemsRepository->findById($this->itemId);
    }

    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $form->addText("alt", "Alternativní text")->setRequired(false);
        $form->addText("image_description", "Popis obrázku")->setRequired(false);
        return $this::createEditForm($form, $this->activeRow, "Aktualizovat změny");
    }

    /**
     * @throws InvalidLinkException
     * @throws AbortException
     */
    public function success(\Nette\Application\UI\Form $form, EditItemFormData $editItemFormData): void {
        $this->successTemplate($form, $editItemFormData->iterable(), new FormMessage("Daná položka byla úspěšně aktualizována.", "Dána položka nebyla z neznámého důvodu aktualizována."), null, $this->itemId);
    }
}