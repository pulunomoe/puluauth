<?php

namespace App\Adapter\Administration\Slim;

use App\Adapter\Administration\Slim\Middleware\AuthenticationMiddleware;
use App\Adapter\Administration\Slim\Middleware\ExceptionHandleMiddleware;
use App\Application\Mediator\AuthenticationMediator;
use App\Application\Port\Administration\AdministrationPort;
use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

readonly class SlimAdministration implements AdministrationPort
{
    /**
     * @throws Exception
     */
    public function __construct(
        private App $app
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function registerMiddlewares(): void
    {
        $authenticationMediator = $this->app->getContainer()->get(AuthenticationMediator::class);
        $authenticationMiddleware = new AuthenticationMiddleware($authenticationMediator);
        $this->app->add($authenticationMiddleware);

        $logger = $this->app->getContainer()->get(Logger::class);
        $exceptionHandleMiddleware = new ExceptionHandleMiddleware($logger, $_ENV['DEBUG'] == 'true');
        $this->app->add($exceptionHandleMiddleware);
    }

    public function registerRoutes(): void
    {
        $administratorController = new AdministratorController($this->app->getContainer());
        $this->app->get('/administrators', [$administratorController, 'findOneByCode']);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->app->handle($request);
    }
}
