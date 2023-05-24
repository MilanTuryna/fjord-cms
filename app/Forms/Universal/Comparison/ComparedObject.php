<?php


namespace App\Forms\Universal\Comparison;


/**
 * Class ComparedObject
 * @package App\Forms\Universal\Comparison
 */
class ComparedObject
{
    public string $value;
    public ?string $date;
    public ?string $additionalInfo;

    /**
     * ComparedObject constructor.
     * @param string $value
     * @param string|null $date
     * @param string|null $additionalInfo
     */
    public function __construct(string $value, ?string $date = null, ?string $additionalInfo = null)
    {
        $this->value = $value;
        $this->date = $date;
        $this->additionalInfo = $additionalInfo;
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->value;
    }
}