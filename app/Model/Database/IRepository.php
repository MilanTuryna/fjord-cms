<?php

namespace App\Model\Database;

interface IRepository
{
    public function insert(iterable $data): mixed;
    public function updateById(int $id, iterable $data): array;
    public function deleteById(int $id): int;
}