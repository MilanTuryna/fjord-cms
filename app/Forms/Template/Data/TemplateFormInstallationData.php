<?php

namespace App\Forms\Template\Data;

use App\Model\Database\Entity;
use Nette\Http\FileUpload;

class TemplateFormInstallationData extends Entity
{
    public FileUpload $installation_zip; // zip of template with index.json
}