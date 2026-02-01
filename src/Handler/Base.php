<?php

namespace Nadi\CodeIgniter\Handler;

use Nadi\CodeIgniter\Nadi;

class Base
{
    public function store(array $data): void
    {
        Nadi::getInstance()->store($data);
    }

    public function hash(string $value): string
    {
        return sha1($value);
    }
}
