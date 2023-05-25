<?php

namespace App\Model\Database;

interface IRepository
{
    public function deleteById(int $id): int;
}