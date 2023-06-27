<?php


namespace App\Model\Database\Repository\Template\Entity;


use App\Forms\Dynamic\Data\AttributeData;
use App\Forms\Dynamic\Enum\InputType;
use App\Model\Database\Entity;

/**
 * Class PageVariable
 * @package App\Model\Database\Repository\Template\Entity
 */
class PageVariable extends Entity
{
    const INPUT_TYPES = AttributeData::INPUT_TYPES;

    const id_name = "id_name";
    const title = "title";
    const description = "description";
    const content = "content";
    const input_type = "input_type";
    const required = "required";
    const page_id = "page_id";
    const id = "id";

    public string $id_name;
    public string $title;
    public string $description;
    public string $content;
    public string $input_type; // use self::INPUT_TYPES
    public string $required;
    public string $page_id;
    public int $id;
}