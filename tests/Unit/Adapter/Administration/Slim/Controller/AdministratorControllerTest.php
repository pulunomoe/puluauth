<?php

namespace Tests\Unit\Adapter\Administration\Slim\Controller;

use App\Adapter\Administration\Slim\Controller\AdministratorController;
use App\Adapter\Administration\Slim\Exception\HttpBadRequestException;
use App\Application\Exception\AdapterException;
use App\Application\UseCase\Administration\Administrator\FindOneAdministratorByCode;
use App\Domain\Administrator\Administrator;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Psr7TestCase;
use Tests\Unit\Domain\Administrator\AdministratorTest;

class AdministratorControllerTest extends Psr7TestCase
{
    private FindOneAdministratorByCode $findAdministrator;
    private AdministratorController $administratorController;

    private Administrator $administrator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->findAdministrator = $this->createMock(FindOneAdministratorByCode::class);

        $this->administratorController = new AdministratorController($this->findAdministrator);

        $this->administrator = AdministratorTest::createAdministrator();
    }

    private function createServerRequest(): ServerRequestInterface
    {
        return self::$psr17Factory->createServerRequest('GET', '/administrators');
    }

    /**
     * @throws AdapterException
     * @throws HttpBadRequestException
     */
    public function testFindOneByCodeSuccessful(): void
    {
        $this->findAdministrator->method('execute')
            ->willReturn($this->administrator);

        $request = $this->createServerRequest()
            ->withQueryParams(['code' => $this->administrator->getCode()->getValue()]);

        $response = $this->administratorController->findOneByCode($request);

        $expectedResponse = $this->createJsonResponse(200, $this->administrator->serialize());

        $this->assertResponseEquals($expectedResponse, $response);
    }

    /**
     * @throws AdapterException
     * @throws HttpBadRequestException
     */
    public function testFindOneByCodeWithNonexistentCode(): void
    {
        $this->findAdministrator->method('execute')
            ->willReturn(null);

        $request = $this->createServerRequest()
            ->withQueryParams(['code' => '00000000-0000-0000-0000-000000000000']);

        $response = $this->administratorController->findOneByCode($request);

        $expectedResponse = $this->createJsonResponse(200, []);

        $this->assertResponseEquals($expectedResponse, $response);
    }

    /**
     * @throws AdapterException
     */
    public function testFindOneByCodeWithEmptyQueryParam(): void
    {
        $request = $this->createServerRequest();

        $this->expectException(HttpBadRequestException::class);
        $this->administratorController->findOneByCode($request);
    }

    /**
     * @throws AdapterException
     */
    public function testFindOneByCodeWithInvalidQueryParam(): void
    {
        $request = $this->createServerRequest()
            ->withQueryParams(['foo' => 'boo-kee']);

        $this->expectException(HttpBadRequestException::class);
        $this->administratorController->findOneByCode($request);
    }

    /**
     * @throws HttpBadRequestException
     */
    public function testFindOneByCodeWithAdapterException(): void
    {
        $this->findAdministrator->method('execute')
            ->willThrowException(new AdapterException());

        $request = $this->createServerRequest()
            ->withQueryParams(['code' => $this->administrator->getCode()->getValue()]);

        $this->expectException(AdapterException::class);
        $this->administratorController->findOneByCode($request);
    }
}
