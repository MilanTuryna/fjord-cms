<?php


namespace App\Forms\Universal\Comparison;


use JetBrains\PhpStorm\Pure;

/**
 * Class InputComparison
 * @package App\Forms\Universal\Comparison
 */
class InputComparison
{
    const TYPE_INPUT = "TYPE_INPUT";
    const TYPE_ARRAY = "TYPE_ARRAY";

    public string $label;
    public string $inputType;
    public array $values;

    /**
     * InputComparison constructor.
     * @param string $inputLabel
     * @param array $values
     * @param string $comparisonInputType
     */
    #[Pure] public function __construct(string $inputLabel, array $values, string $comparisonInputType = self::TYPE_INPUT) {
        $this->label = $inputLabel;
        $this->inputType = $comparisonInputType;
        foreach ($values as $value) {
            if(!($value instanceof ComparedObject)) $value = new ComparedObject($value);
            if(!$value) $value = "";
            $this->values[] = $value;
        }
    }

    public function isString(): bool {
        return $this->inputType === self::TYPE_INPUT;
    }

    public function isList(): bool {
        return $this->inputType === self::TYPE_ARRAY;
    }

    /**
     * @return ComparedObject[]
     */
    public function getValues(): array {
        return $this->values;
    }
}