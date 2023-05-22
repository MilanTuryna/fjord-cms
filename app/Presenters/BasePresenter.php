<?php


namespace App\Presenters;


use Nette\Application\Helpers;
use Nette\Application\UI\Presenter;

/**
 * Class BasePresenter
 * @package App\Presenters
 */
class BasePresenter extends Presenter
{
    /**
     * @param bool $val
     */
    protected function enableFlashes(bool $val = true): void  {
        $this->template->disableFlashes = !$val;
    }

    public function startup()
    {
        parent::startup();

        $this->enableFlashes();
        $this->template->addFunction("declension", function ($count, $words) {
            $count = abs((int)$count);
            if ($count == 1) return $words[0];
            if ($count < 5 && $count > 0) return $words[1];
            return $words[2];
        });
    }

    /**
     * @return array
     */
    public function formatTemplateFiles(): array
    {
        [$module, $presenter] = Helpers::splitName($this->getName());

        // ex. Admin/templates/Application/Settings/file.latte
        $module = explode(":", $module);
        unset($module[0]);
        if(!empty($module)) $module = implode("/", $module);

        $dir = dirname(static::getReflection()->getFileName());
        $dir = is_dir("$dir/templates") ? $dir : dirname($dir);
        return !empty($module) ? [
            "$dir/templates/$module/$presenter/$this->view.latte",
            "$dir/templates/$module/$presenter.$this->view.latte",
        ] : [
            "$dir/templates/$presenter/$this->view.latte"
        ];
    }
}