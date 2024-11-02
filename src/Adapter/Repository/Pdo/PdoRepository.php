<?php

namespace App\Adapter\Repository\Pdo;

use PDO;

abstract readonly class PdoRepository
{
    public function __construct(protected PDO $pdo)
    {
    }
}
