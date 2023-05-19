<?php


namespace App\Forms;


use Nette\Application\UI\Presenter;

abstract class Form
{
    public function __construct(protected Presenter $presenter) {
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