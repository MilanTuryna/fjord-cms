<?php


namespace App\Model\Templating\DataHint;

use App\Model\Database\EAV\DynamicEntityFactory;
use App\Model\Database\Repository\Settings\Entity\GlobalSettings;

// TODO: better optimalization and use collections instead for DB
class FjordTemplateProviderData
{
    const GALLERIES = "galleries";
    const DYNAMIC_ENTITY_FACTORY = "dynamicEntityFactory";
    const SETTINGS = "settings" ;
    const PARAMETERS = "parameters";

    public array $galleries; // Array of all galleries
    public DynamicEntityFactory $dynamicEntityFactory;
    public GlobalSettings $settings;
    public array $parameters; // associative array
}