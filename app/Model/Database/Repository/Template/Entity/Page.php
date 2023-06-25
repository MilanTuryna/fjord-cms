<?php


namespace App\Model\Database\Repository\Template\Entity;


use App\Model\Database\Entity;

/**
 * Class Page
 * @package App\Model\Database\Repository\Template\Entity
 */
class Page extends Entity
{
    const name = "name", route="", description = "description", output_content = "output_content", output_type = "output_type", template_id = "template_id";

    public string $name;
    public string $route;
    public string $description;
    public string $output_content;
    public string $output_type;
    public int $template_id;
    public int $id;
}