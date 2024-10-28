<?php

namespace Tests;

use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @property-read PDOStatement&Stub $stmt
 */
abstract class PdoTestCase extends TestCase
{
    use PdoTestTrait;

    protected PDOStatement $stmt;
    protected PDO $pdo;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->setUpPdo();
    }
}
