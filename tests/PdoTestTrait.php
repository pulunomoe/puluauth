<?php

namespace Tests;

use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\Exception;

trait PdoTestTrait
{
    /**
     * @throws Exception
     */
    protected function setUpPdo(): void
    {
        $this->stmt = $this->createStub(PDOStatement::class);
        $this->stmt->method('bindValue')
            ->willReturn(true);
        $this->stmt->method('execute')
            ->willReturn(true);

        $this->pdo = $this->createStub(PDO::class);
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);
    }
}
