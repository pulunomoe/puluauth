<?php

use App\Adapter\Administration\Slim\SlimAdministration;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\ResponseEmitter;

$app = require __DIR__ . '/../config/bootstrap.php';

$serverRequestCreator = ServerRequestCreatorFactory::create();
$serverRequest = $serverRequestCreator->createServerRequestFromGlobals();

$administrationPort = new SlimAdministration($app);
$administrationPort->registerMiddlewares();
$administrationPort->registerRoutes();
$response = $administrationPort->handle($serverRequest);

$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
