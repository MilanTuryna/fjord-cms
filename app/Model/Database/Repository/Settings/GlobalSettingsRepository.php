<?php


namespace App\Model\Database\Repository\Settings;


use App\Model\Database\Repository;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;

/**
 * Class GlobalSettingsRepository
 * @package App\Model\Database\Repository\Settings
 */
class GlobalSettingsRepository extends Repository
{
    /**
     * GlobalSettingsRepository constructor.
     * @param Explorer $explorer
     */
    public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_global_settings", $explorer);
    }

    /**
     * @return ActiveRow|Repository\Settings\Entity\GlobalSettings
     */
    public function getActualSettings(): ?ActiveRow
    {
        return $this->explorer->table($this->table)->order("id DESC")->limit(1)->fetch();
    }
}