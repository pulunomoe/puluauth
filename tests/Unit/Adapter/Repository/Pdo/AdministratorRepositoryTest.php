<?php

namespace Tests\Unit\Adapter\Repository\Pdo;

use App\Adapter\Repository\Pdo\AdministratorRepository;
use App\Application\Exception\AdapterException;
use App\Domain\Administrator\Administrator;
use PDOException;
use Tests\PdoTestCase;
use Tests\Unit\Domain\Administrator\AdministratorTest;

class AdministratorRepositoryTest extends PdoTestCase
{
    private AdministratorRepository $administratorRepository;

    private Administrator $administrator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->administratorRepository = new AdministratorRepository($this->pdo);

        $this->administrator = AdministratorTest::createAdministrator();
    }

    /**
     * @throws AdapterException
     */
    public function testFindOneByCodeSuccessful(): void
    {
        $this->stmt->method('fetch')
            ->willReturn([
                'id' => $this->administrator->getId(),
                'code' => $this->administrator->getCode()->getValue(),
                'name' => $this->administrator->getName(),
                'email' => $this->administrator->getEmail()
            ]);

        $result = $this->administratorRepository->findOneByCode($this->administrator->getCode());

        $this->assertEquals($this->administrator, $result);
    }

    /**
     * @throws AdapterException
     */
    public function testFindOneByCodeWithNonExistantCode(): void
    {
        $this->stmt->method('fetch')
            ->willReturn(false);

        $this->assertNull($this->administratorRepository->findOneByCode($this->administrator->getCode()));
    }

    public function testFindOneByCodeWithPdoException(): void
    {
        $this->stmt->method('execute')
            ->willThrowException(new PDOException());

        $this->expectException(AdapterException::class);
        $this->administratorRepository->findOneByCode($this->administrator->getCode());
    }
}
