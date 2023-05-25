<?php

namespace App\Model\Database;

interface IRepository
{
    public function insert(array $data): int;
    public function updateById(int $id, iterable $data): int;
    public function deleteById(int $id): int;
}