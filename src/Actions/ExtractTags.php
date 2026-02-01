<?php

namespace Nadi\CodeIgniter\Actions;

class ExtractTags
{
    public static function from($target): array
    {
        if (is_object($target) && method_exists($target, 'tags')) {
            return $target->tags();
        }

        return [];
    }

    public static function fromArray(array $data): array
    {
        return [];
    }
}
