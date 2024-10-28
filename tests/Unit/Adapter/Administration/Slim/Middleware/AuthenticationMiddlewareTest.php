<?php

namespace Tests\Unit\Adapter\Administration\Slim\Middleware;

use App\Adapter\Administration\Slim\Exception\HttpUnauthorizedException;
use App\Adapter\Administration\Slim\Middleware\AuthenticationMiddleware;
use App\Application\Exception\AuthenticationException;
use App\Application\Exception\MediatorException;
use App\Application\Mediator\AuthenticationMediator;
use App\Domain\Administrator\Administrator;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Unit\Domain\Administrator\AdministratorTest;

class AuthenticationMiddlewareTest extends MiddlewareTestCase
{
    private AuthenticationMediator $authenticationMediator;
    private AuthenticationMiddleware $authenticationMiddleware;

    private Administrator $administrator;

    private string $apiKey = 'this-is-an-api-key';
    private string $signature = 'this-is-a-signature';

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticationMediator = $this->createStub(AuthenticationMediator::class);

        $this->authenticationMiddleware = new AuthenticationMiddleware($this->authenticationMediator);

        $this->administrator = AdministratorTest::createAdministrator();
    }

    private function addValidAuthentication(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request->withHeader('PULU-API-KEY', $this->apiKey)
            ->withHeader('PULU-TIMESTAMP', time())
            ->withHeader('PULU-SIGNATURE', $this->signature);
    }

    /**
     * @throws MediatorException
     * @throws HttpUnauthorizedException
     */
    public function testSuccessful(): void
    {
        $request = $this->addValidAuthentication($this->request);

        $this->authenticationMediator->method('authenticate')
            ->willReturn($request->withAttribute('administrator', $this->administrator));

        $this->handler->method('handle')
            ->with($this->callback(function (ServerRequestInterface $request) {
                return $this->administrator == $request->getAttribute('administrator');
            }))
            ->willReturn($this->response);

        $response = $this->authenticationMiddleware->process($request, $this->handler);
        $this->assertResponseEquals($this->response, $response);
    }

    /**
     * @throws HttpUnauthorizedException
     */
    public function testWithMediatorException(): void
    {
        $this->authenticationMediator->method('authenticate')
            ->willThrowException(new MediatorException());

        $request = $this->addValidAuthentication($this->request);

        $this->expectException(MediatorException::class);
        $this->authenticationMiddleware->process($request, $this->handler);
    }

    /**
     * @throws MediatorException
     */
    public function testWithAuthenticationException(): void
    {
        $this->authenticationMediator->method('authenticate')
            ->willThrowException(new AuthenticationException());

        $request = $this->addValidAuthentication($this->request);

        $this->expectException(HttpUnauthorizedException::class);
        $this->authenticationMiddleware->process($request, $this->handler);
    }
}
