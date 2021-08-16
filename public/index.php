<?php

use Com\Pulunomoe\PuluAuth\Auth\Repository\AccessTokenRepository;
use Com\Pulunomoe\PuluAuth\Auth\Repository\ClientRepository;
use Com\Pulunomoe\PuluAuth\Auth\Repository\ScopeRepository;
use Com\Pulunomoe\PuluAuth\Controller\AuthController;
use Com\Pulunomoe\PuluAuth\Controller\ClientController;
use Defuse\Crypto\Key;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extension\DebugExtension;

session_start();

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/dbconfig.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$twig = Twig::create(__DIR__ . '/../templates/', ['debug' => true]);
$twig->addExtension(new DebugExtension());
$twig->getEnvironment()->addGlobal('session', $_SESSION);
$app->add(TwigMiddleware::create($app, $twig));

$clientRepository = new ClientRepository();
$scopeRepository = new ScopeRepository();
$accessTokenRepository = new AccessTokenRepository();

$privateKey = new CryptKey('file://'.__DIR__.'/../keys/private.key', null, false);
$encryptionKey = trim(file_get_contents('file://'.__DIR__.'/../keys/defuse.key'));
$encryptionKey = Key::loadFromAsciiSafeString($encryptionKey);

$clientCredentialsGrant = new ClientCredentialsGrant();
$accessTokenTtl = new DateInterval('PT1H');

$authServer = new AuthorizationServer($clientRepository, $accessTokenRepository, $scopeRepository, $privateKey, $encryptionKey);
$authServer->enableGrantType($clientCredentialsGrant, $accessTokenTtl);

$authController = new AuthController($authServer);
$clientController = new ClientController($pdo);

$app->post('/access_token', [$authController, 'accessToken']);

$app->get('/clients', [$clientController, 'index']);
$app->get('/clients/view/{clientId}', [$clientController, 'view']);
$app->get('/clients/form[/{clientId}]', [$clientController, 'form']);
$app->post('/clients/form', [$clientController, 'formPost']);
$app->get('/clients/newsecret/{clientId}', [$clientController, 'newSecret']);
$app->post('/clients/newsecret', [$clientController, 'newSecretPost']);
$app->get('/clients/delete/{clientId}', [$clientController, 'delete']);
$app->post('/clients/delete', [$clientController, 'deletePost']);

$app->run();
