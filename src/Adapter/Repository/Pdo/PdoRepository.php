<?php

namespace App\Adapter\Repository\Pdo;

use PDO;

abstract class PdoRepository
{
    public function __construct(protected readonly PDO $pdo)
    {
    }
}
