<?php

namespace App\Domain\Service;

use Ramsey\Uuid\Uuid;

class CodeGenerator
{
    public static function generate(): string
    {
        return gmp_strval(gmp_init(str_replace('-', '', Uuid::uuid4()->toString()), 16), 62);
    }
}
