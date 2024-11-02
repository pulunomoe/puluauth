<?php

namespace Tests\Unit\Adapter\Administration\Slim;

use App\Adapter\Administration\Slim\AdministratorController;
use App\Adapter\Administration\Slim\Middleware\AuthenticationMiddleware;
use App\Adapter\Administration\Slim\Middleware\ExceptionHandleMiddleware;
use App\Adapter\Administration\Slim\SlimAdministration;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Interfaces\RouteInterface;

class SlimAdministrationTest extends TestCase
{
    private App $app;
    private SlimAdministration $slimAdministration;

    /**
     * @throws Exception
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(function ($className) {
                return $this->createStub($className);
            });

        $this->app = $this->createMock(App::class);
        $this->app->method('getContainer')->willReturn($container);

        $this->slimAdministration = new SlimAdministration($this->app);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testRegisterMiddlewares(): void
    {
        $expectedMiddlewares = [
            AuthenticationMiddleware::class,
            ExceptionHandleMiddleware::class,
        ];

        $this->app->expects($this->exactly(2))
            ->method('add')
            ->willReturnCallback(function ($middleware) use (&$expectedMiddlewares) {
                $expectedMiddleware = array_shift($expectedMiddlewares);
                $this->assertInstanceOf($expectedMiddleware, $middleware);
                return $this->app;
            });

        $this->slimAdministration->registerMiddlewares();
    }

    public function testRegisterRoutes(): void
    {
        $expectedRoutes = [
            ['/administrators', [AdministratorController::class, 'findOneByCode']],
        ];

        $this->app->expects($this->exactly(1))
            ->method('get')
            ->willReturnCallback(function ($route, $handler) use (&$expectedRoutes) {
                $expectedRoute = array_shift($expectedRoutes);
                $this->assertEquals($expectedRoute[0], $route);
                $this->assertInstanceOf($expectedRoute[1][0], $handler[0]);
                $this->assertEquals($expectedRoute[1][1], $handler[1]);
                return $this->createStub(RouteInterface::class);
            });

        $this->slimAdministration->registerRoutes();
    }

    /**
     * @throws Exception
     */
    public function testHandle(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $response = $this->createStub(ResponseInterface::class);
        $this->app->method('handle')
            ->willReturn($response);

        $result = $this->slimAdministration->handle($request);
        $this->assertEquals($response, $result);
    }
}
