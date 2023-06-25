<?php


namespace App\Model\Templating\DataHint;

use App\Model\Database\Repository\Settings\Entity\GlobalSettings;

// TODO: better optimalization and use collections instead for DB
class FjordTemplateProviderData
{
    public array $galleries; // Array of all galleries
    public array $entities; // EAV entities
    public GlobalSettings $settings;
}