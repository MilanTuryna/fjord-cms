<?php


namespace App\Model\Templating\Schema;


use App\Forms\Dynamic\Data\AttributeData;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

/**
 * Class IndexJsonSchema
 * @package App\Model\Templating\Schema
 */
class IndexJsonSchema
{
    /**
     * @return Structure
     */
    public static function getSchema(): Structure {
        return Expect::structure([
            "title" => Expect::string(),
            "author" => Expect::structure(
                [
                    "name" => Expect::string(),
                    "email" => Expect::string(),
                    "website" => Expect::string(),
                ]
            )->required(false),
            "pages" => Expect::listOf(Expect::structure(
                [
                    "page_title" => Expect::string(),
                    "description" => Expect::string()->required(false),
                    "output_content" => Expect::string(),
                    "output_type" => Expect::anyOf(["SRC", "PATH"])->required(false),
                ]
            )),
            "eav" => Expect::listOf(
                Expect::structure(
                    [
                        "entity_name" => Expect::string(),
                        "entity_description" => Expect::string()->required(false),
                        "attributes" => Expect::listOf(
                            Expect::structure([
                                "id_name" => Expect::string(),
                                "data_type" => Expect::anyOf(["string", "bool", "int", "float"]),
                                "description" => Expect::string()->required(false),
                                "placeholder" => Expect::string()->required(false),
                                "generate_value" => Expect::anyOf(array_keys(AttributeData::GENERATED_VALUES))->required(false),
                                "preset_value" => Expect::string()->required(false),
                                "required" => Expect::bool()
                            ])
                        )
                    ]
                )
            ),
            "description" => Expect::string()->required(false),
            "version" => Expect::string()->required(true),
        ]);
    }
}