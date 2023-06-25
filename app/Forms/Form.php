<?php


namespace App\Forms;


use Contributte\Forms\Controls\DateTime\DateTimeInput;
use Nette\Application\UI\Presenter;

abstract class Form
{
    public function __construct(protected Presenter $presenter) {
    }

    /**
     * @param \Nette\Application\UI\Form $form
     * @param string $name
     * @param string|null $label
     * @return DateTimeInput
     */
    public static function createDateTime(\Nette\Application\UI\Form &$form, string $name, ?string $label = null): DateTimeInput
    {
        $control = new DateTimeInput($label);
        $form->addComponent($control, $name);
        // TODO: test
        return $control;
    }

    /**
     * @param array $values ([input_name => value])
     */
    public function setDefaultValues(\Nette\Application\UI\Form &$form, array $values)
    {
        foreach ($values as $k => $v) $form[$k]->setDefaultValue($v);
    }

    /**
     * @param $class
     * @return \Nette\Application\UI\Form
     */
    public static function staticCreate($class): \Nette\Application\UI\Form {
        $form = new \Nette\Application\UI\Form();
        $form->onSuccess[] = [$class, 'success'];
        $form->onError[] = function() use ($form, $class) {
            foreach($form->getErrors() as $error) $class->presenter->flashMessage($error, FlashMessages::ERROR);
        };
        return $form;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function create(): \Nette\Application\UI\Form {
        return self::staticCreate($this);
    }
}