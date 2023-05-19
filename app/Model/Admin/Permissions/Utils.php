<?php


namespace App\Model\Admin\Permissions;


class Utils
{
    const FULL_PERM = "*";
    const SPECIAL_WITHOUT_PERMISSION = "system.special_without_permission";

    /**
     * @param array $permArray
     * @param string $node
     * @return bool
     */
    public static function checkPermission(array $permArray, string $node): bool {
        return in_array($node, $permArray) || in_array(self::FULL_PERM, $permArray) || $node === self::SPECIAL_WITHOUT_PERMISSION;
    }

    /**
     * Method used to generate message if user don't have permission.
     * @param string $node
     * @return string
     */
    public static function getNoPermMessage(string $node): string {
        return 'K této sekci nemáš přístup.';
    }

    /**
     * Method used to parse array (in string, from MySQL) to classic array.
     * @param string $unparsedList
     * @return array
     */
    public static function listToArray(string $unparsedList): array {
        return array_filter(explode(",", preg_replace('/\s+/', '', $unparsedList)));
    }

    /**
     * @param array $parsedList
     * @return string
     */
    public static function arrayToUnparsedList(array $parsedList): string {
        return implode(",", $parsedList);
    }
}