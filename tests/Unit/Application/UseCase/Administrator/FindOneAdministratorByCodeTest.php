<?php

namespace Tests\Unit\Application\UseCase\Administrator;

use App\Application\Exception\AdapterException;
use App\Application\Port\Repository\AdministratorRepositoryPort;
use App\Application\UseCase\Administration\Administrator\FindOneAdministratorByCode;
use App\Domain\Administrator\Administrator;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Domain\Administrator\AdministratorTest;

class FindOneAdministratorByCodeTest extends TestCase
{
    private AdministratorRepositoryPort $administratorRepositoryPort;
    private FindOneAdministratorByCode $findOneAdministratorByCode;

    private Administrator $administrator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->administratorRepositoryPort = $this->createStub(AdministratorRepositoryPort::class);

        $this->findOneAdministratorByCode = new FindOneAdministratorByCode($this->administratorRepositoryPort);

        $this->administrator = AdministratorTest::createAdministrator();
    }

    /**
     * @throws AdapterException
     */
    public function testExecuteSuccessful(): void
    {
        $this->administratorRepositoryPort
            ->method('findOneByCode')
            ->willReturn($this->administrator);

        $result = $this->findOneAdministratorByCode->execute($this->administrator->getCode());
        $this->assertEquals($this->administrator, $result);
    }

    /**
     * @throws AdapterException
     */
    public function testExecuteWithNonExistantCode(): void
    {
        $this->administratorRepositoryPort
            ->method('findOneByCode')
            ->willReturn(null);

        $result = $this->findOneAdministratorByCode->execute($this->administrator->getCode());
        $this->assertNull($result);
    }

    public function testExecuteWithAdapterException(): void
    {
        $this->administratorRepositoryPort
            ->method('findOneByCode')
            ->willThrowException(new AdapterException());

        $this->expectException(AdapterException::class);
        $this->findOneAdministratorByCode->execute($this->administrator->getCode());
    }
}
