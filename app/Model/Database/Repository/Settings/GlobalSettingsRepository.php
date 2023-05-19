<?php


namespace App\Model\Database\Repository\Settings;


use App\Model\Database\AbstractRepository;
use Nette\Database\Explorer;

/**
 * Class GlobalSettingsRepository
 * @package App\Model\Database\Repository\Settings
 */
class GlobalSettingsRepository extends AbstractRepository
{
    /**
     * GlobalSettingsRepository constructor.
     * @param Explorer $explorer
     */
    public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_global_settings", $explorer);
    }
}