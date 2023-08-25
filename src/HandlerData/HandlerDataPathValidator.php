<?php

namespace Ingenerator\ApiEmulator\HandlerData;

use function preg_match;

class HandlerDataPathValidator
{

    public static function isValid(string $path): bool
    {
        return (bool) preg_match('#^[a-zA-Z0-9/_-]+$#', $path);
    }
}
