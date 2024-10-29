<?php

use App\Adapter\Administration\Slim\Controller\AdministratorController;
use App\Adapter\Administration\Slim\Middleware\AuthenticationMiddleware;
use App\Adapter\Administration\Slim\Middleware\ExceptionHandlingMiddleware;
use App\Adapter\Repository\Pdo\AdministratorRepository;
use App\Application\Mediator\AuthenticationMediator;
use App\Application\Port\Repository\AdministratorRepositoryPort;
use App\Application\UseCase\Administration\Administrator\FindOneAdministratorByCode;
use App\Mediator\Authentication\ApiKeyAuthenticationMediator;
use Defuse\Crypto\Key;
use Monolog\Handler\FilterHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Container\ContainerInterface;

return [
    PDO::class => function (): PDO {
        return new PDO(
            'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4',
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    },

    Logger::class => function (): Logger {
        $logger = new Logger('puluauth');

        $logDirectory = __DIR__ . '/../logs/';

        $appLogHandler = new StreamHandler($logDirectory . 'app.log', Level::Info);
        $logger->pushHandler(new FilterHandler($appLogHandler, Level::Info, Level::Notice));

        $errorLogHandler = new StreamHandler($logDirectory . 'error.log', Level::Error);
        $logger->pushHandler(new FilterHandler($errorLogHandler, Level::Error, Level::Critical));

        return $logger;
    },

    Psr17Factory::class => function (): Psr17Factory {
        return new Psr17Factory();
    },

    ServerRequestCreator::class => function (ContainerInterface $container): ServerRequestCreator {
        return new ServerRequestCreator(
            $container->get(Psr17Factory::class), // ServerRequestFactory
            $container->get(Psr17Factory::class), // UriFactory
            $container->get(Psr17Factory::class), // UploadedFileFactory
            $container->get(Psr17Factory::class)  // StreamFactory
        );
    },

    AuthenticationMediator::class => function (ContainerInterface $container): AuthenticationMediator {
        return new ApiKeyAuthenticationMediator(
            $container->get(PDO::class),
            Key::loadFromAsciiSafeString($_ENV['ENCRYPTION_KEY']),
            $_ENV['MAX_REQUEST_AGE']
        );
    },

    AdministratorRepositoryPort::class => function (ContainerInterface $container): AdministratorRepositoryPort {
        return new AdministratorRepository($container->get(PDO::class));
    },

    FindOneAdministratorByCode::class => function (ContainerInterface $container): FindOneAdministratorByCode {
        return new FindOneAdministratorByCode($container->get(AdministratorRepositoryPort::class));
    },

    AuthenticationMiddleware::class => function (ContainerInterface $container): AuthenticationMiddleware {
        return new AuthenticationMiddleware(
            $container->get(AuthenticationMediator::class)
        );
    },

    ExceptionHandlingMiddleware::class => function (ContainerInterface $container): ExceptionHandlingMiddleware {
        return new ExceptionHandlingMiddleware(
            $container->get(Logger::class),
            $_ENV['DEBUG']
        );
    },

    AdministratorController::class => function (ContainerInterface $container): AdministratorController {
        return new AdministratorController($container->get(FindOneAdministratorByCode::class));
    }
];
