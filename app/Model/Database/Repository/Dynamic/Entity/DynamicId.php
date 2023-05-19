<?php


namespace App\Model\Database\Repository\Dynamic\Entity;


use App\Model\Database\Entity;
use Exception;

/**
 * Class DynamicId
 * @package App\Model\Database\Repository\Dynamic\Entity
 */
class DynamicId extends Entity
{
    const created = "created";
    const row_unique = "row_unique";
    const entity_id = "entity_id";
    const id = "id";

    public string $created;
    public int $id;
    public int $entity_id;
    public string $row_unique;

    /**
     * @throws Exception
     */
    public static function createRowUnique($len = 13): string {
        // used solution on: https://www.php.net/manual/en/function.uniqid.php#120123
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($len / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($len / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $len);
    }
}