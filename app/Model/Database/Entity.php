<?php

namespace App\Model\Database;

use Exception;
use ReflectionException;

/**
 * Class Entity
 * @package App\Model\Database
 */
abstract class Entity
{
    const id = "id";
    public int $id;

    /**
     * @param bool $without_
     * @return array
     */
    public function iterable(bool $without_ = false): array {
        $vars = get_object_vars($this);
        if($without_) {
            foreach ($vars as $key => $value)
                if(isset($key[0]) && $key[0] === "_") unset($vars[$key]);
        }

        return $vars;
    }

    /**
     * @param object $object
     * @param bool $strict
     * @param bool $throwError If variable in entity doesn't exist
     * @return void
     * @throws ReflectionException
     */
    public function createFrom(object $object, bool $throwError = false,  bool $strict = true): void {
        foreach (get_object_vars($object) as $key => $variable) {
            if(!$strict || property_exists($this, $key) && $key[0] !== "_") {
                $reflection = new \ReflectionProperty(get_class($this), $key);
                settype($object->{$key}, $reflection->getType()->getName());
                $this->{$key} = $object->{$key};
            }
        }
    }
}