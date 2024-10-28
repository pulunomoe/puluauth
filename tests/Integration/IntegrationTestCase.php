<?php

namespace Tests\Integration;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\BadFormatException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Key;
use PDO;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Tests\Psr7TestCase;

class IntegrationTestCase extends Psr7TestCase
{
    protected static App $app;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws EnvironmentIsBrokenException
     * @throws BadFormatException
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        global $app;
        self::$app = $app;

        $pdo = self::$app->getContainer()->get(PDO::class);
        $pdo->exec("DELETE FROM administrators WHERE code = 'root'");

        $key = Key::loadFromAsciiSafeString($_ENV['ENCRYPTION_KEY']);

        $stmt = $pdo->prepare('INSERT INTO administrators (code, name, email, api_key, secret_key) VALUES (:code, :name, :email, :api_key, :secret_key)');
        $stmt->execute([
            'code' => 'root',
            'name' => 'root',
            'email' => 'root@example.com',
            'api_key' => 'root-api-key',
            'secret_key' => Crypto::encrypt('root-secret-key', $key),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function tearDownAfterClass(): void
    {
        $pdo = self::$app->getContainer()->get(PDO::class);
        $pdo->exec("DELETE FROM administrators WHERE code = 'root'");
    }

    protected function addValidAuthentication(ServerRequestInterface $request): ServerRequestInterface
    {
        $timestamp = time();
        return $request->withHeader('PULU-API-KEY', 'root-api-key')
            ->withHeader('PULU-TIMESTAMP', time())
            ->withHeader('PULU-SIGNATURE', hash_hmac('sha256', 'root-api-key' . $timestamp, 'root-secret-key'));
    }
}
