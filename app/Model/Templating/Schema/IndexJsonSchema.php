<?php


namespace App\Model\Templating\Schema;


use App\Forms\Dynamic\Data\AttributeData;
use App\Forms\Dynamic\Enum\InputType;
use App\Model\Database\EAV\DataType;
use App\Model\Database\Repository\Template\Entity\PageVariable;
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
                    "name" => Expect::string(),
                    "route" => Expect::string(),
                    "description" => Expect::string()->required(false),
                    "output_content" => Expect::string(),
                    "output_type" => Expect::anyOf(["SRC", "PATH"])->required(false),
                    "variables" => Expect::listOf(Expect::structure( // PageVariable
                        [
                            PageVariable::id_name => Expect::string(),
                            PageVariable::title => Expect::string(),
                            PageVariable::description => Expect::string()->required(false),
                            PageVariable::content => Expect::string()->required(false),
                            PageVariable::input_type => Expect::anyOf(InputType::arr()),
                            PageVariable::required => Expect::bool()->required(false),
                        ]
                    ))->required(false)
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
                                "title" => Expect::string(),
                                "data_type" => Expect::anyOf(DataType::arr()),
                                "input_type" => Expect::anyOf(InputType::arr()),
                                "description" => Expect::string()->required(false),
                                "placeholder" => Expect::string()->required(false),
                                "allowed_translation" => Expect::bool()->required(false),
                                "generate_value" => Expect::anyOf(array_keys(AttributeData::GENERATED_VALUES))->required(false),
                                "preset_value" => Expect::string()->required(false),
                                "required" => Expect::bool()->required(false),
                                "enabled_wysiwyg" => Expect::bool()->required(false),
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