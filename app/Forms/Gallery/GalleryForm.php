<?php


namespace App\Forms\Gallery;


use App\Forms\Form;
use App\Forms\Gallery\Data\ItemFormData;
use App\Model\Database\Repository\Gallery\GalleryRepository;
use App\Model\Database\Repository\Gallery\ItemsRepository;
use JetBrains\PhpStorm\Pure;
use Nette\Application\UI\Presenter;
use Nette\Forms\Container;

/**
 * Class GalleryForm
 * @package App\Forms\Gallery
 */
class GalleryForm extends Form
{
    /**
     * GalleryForm constructor.
     * @param Presenter $presenter
     * @param GalleryRepository $galleryRepository
     * @param ItemsRepository $itemsRepository
     */
    #[Pure] public function __construct(protected Presenter $presenter, private GalleryRepository $galleryRepository, private ItemsRepository $itemsRepository)
    {
        parent::__construct($this->presenter);
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function create(): \Nette\Application\UI\Form
    {
        $form = parent::create();
        $form->addText("name", "Název galerie")->setRequired(true);
        $form->addText("description", "Popis")->setRequired(false);
        $form->addText("URI", "URL adresa")->setRequired(false);
        $items = $form->addMultiplier("_items", function (Container $container, Form $form) {
            $container->addUpload("file_content", "Nahrát obrázek")->addRule(\Nette\Forms\Form::Image)
                ->addRule(\Nette\Forms\Form::MAX_FILE_SIZE, ItemFormData::MAX_FILE_SIZE);
            $container->addText("alt", "Alternativní text")->setRequired(false);
            $container->addText("image_description", "Popis obrázku");
        });
        $form->addMultiUpload("_global-upload")->setRequired(false);
        $items->addCreateButton("Přidat položku")->addClass('btn btn-dark w-100');;
        $form->addSubmit("submit", "Vytvořit galerii");
        return $form;
    }
}