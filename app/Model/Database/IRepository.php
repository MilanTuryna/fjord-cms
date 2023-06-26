<?php

namespace App\Model\Database;

interface IRepository
{
    public function insert(iterable $data): mixed;
    public function updateById(int|string $id, iterable $data): array;
    public function deleteById(int|string $id): int;
}