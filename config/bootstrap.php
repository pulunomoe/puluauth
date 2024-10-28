<?php

use App\Adapter\Administration\Slim\Middleware\AuthenticationMiddleware;
use App\Adapter\Administration\Slim\Middleware\ExceptionHandlingMiddleware;
use App\Adapter\Administration\Slim\Service\JsonResponseFactory;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

Dotenv::createImmutable(__DIR__ . '/../')->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/dependencies.php')->build();
$container = $containerBuilder->build();

JsonResponseFactory::setPsr17Factory($container->get(Psr17Factory::class));

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->add($container->get(AuthenticationMiddleware::class));
$app->add($container->get(ExceptionHandlingMiddleware::class));

$routes = require_once __DIR__ . '/routes.php';
$routes($app);

return $app;
