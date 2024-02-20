<?php

namespace App\Forms\Installation\Select;

class DatabaseSystemSelect
{
    const MYSQL = "mysql";
    const MARIADB = "mariadb";

    public static function selectBox(): array
    {
        return [
            self::MYSQL => "MySQL",
            self::MARIADB => "MariaDB"
        ];
    }
}