<?php


namespace App\Forms\Universal\Comparison;


use JetBrains\PhpStorm\Pure;

/**
 * Class InputComparison
 * @package App\Forms\Universal\Comparison
 */
class InputComparison
{
    public string $label;
    public array $values;

    /**
     * InputComparison constructor.
     * @param string $inputLabel
     * @param array $values
     */
    #[Pure] public function __construct(string $inputLabel, array $values) {
        $this->label = $inputLabel;
        foreach ($values as $value) {
            if(!($value instanceof ComparedObject)) $value = new ComparedObject($value);
            if(!$value) $value = "";
            $this->values[] = $value;
        }
    }

    /**
     * @return ComparedObject[]
     */
    public function getValues(): array {
        return $this->values;
    }
}