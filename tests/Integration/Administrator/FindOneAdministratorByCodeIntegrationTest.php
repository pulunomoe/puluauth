<?php

namespace Tests\Integration\Administrator;

use App\Domain\Service\CodeGenerator;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use PDO;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Integration\IntegrationTestCase;

class FindOneAdministratorByCodeIntegrationTest extends IntegrationTestCase
{
    private static string $code;
    private static string $name = 'Foo Boo Kee';
    private static string $email = 'fbk@example.com';
    private static string $apiKey = 'fbk_api_key';
    private static string $secretKey = 'fbk_secret_key';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $pdo = self::$app->getContainer()->get(PDO::class);

        self::$code = CodeGenerator::generate();
        $key = Key::loadFromAsciiSafeString($_ENV['ENCRYPTION_KEY']);

        $pdo->exec("DELETE FROM administrators WHERE code != 'root'");

        $stmt = $pdo->prepare('INSERT INTO administrators (code, name, email, api_key, secret_key) VALUES (:code, :name, :email, :api_key, :secret_key)');
        $stmt->execute([
            'code' => self::$code,
            'name' => self::$name,
            'email' => self::$email,
            'api_key' => self::$apiKey,
            'secret_key' => Crypto::encrypt(self::$secretKey, $key),
        ]);
        $stmt->execute([
            'code' => CodeGenerator::generate(),
            'name' => 'Shee Rakami',
            'email' => 'shrkm@example.com',
            'api_key' => 'shrkm_api_key',
            'secret_key' => Crypto::encrypt('shrkm_secret_key', $key),
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $pdo = self::$app->getContainer()->get(PDO::class);
        $pdo->exec("DELETE FROM administrators WHERE code != 'root'");
    }

    private function createServerRequest(): ServerRequestInterface
    {
        return self::$psr17Factory->createServerRequest('GET', '/administrators');
    }

    public function testFindOneByCodeSuccessful(): void
    {
        $request = $this->createServerRequest();
        $request = $this->addValidAuthentication($request)
            ->withQueryParams(['code' => self::$code]);

        $response = self::$app->handle($request);

        $expectedResponse = $this->createJsonResponse(200, [
            'code' => self::$code,
            'name' => self::$name,
            'email' => self::$email
        ]);

        $this->assertResponseEquals($expectedResponse, $response);
    }

    public function testFindOneByCodeWithNonexistentCode(): void
    {
        $request = $this->createServerRequest();
        $request = $this->addValidAuthentication($request)
            ->withQueryParams(['code' => '00000000-0000-0000-0000-000000000000']);

        $response = self::$app->handle($request);

        $expectedResponse = $this->createJsonResponse(200, []);

        $this->assertResponseEquals($expectedResponse, $response);
    }

    public function testFindOneByCodeWithEmptyQueryParam(): void
    {
        $request = $this->createServerRequest();
        $request = $this->addValidAuthentication($request);

        $response = self::$app->handle($request);

        $expectedResponse = $this->createJsonResponse(400, [
            'message' => 'Parameter `code` is required'
        ]);

        $this->assertResponseEquals($expectedResponse, $response);
    }

    public function testFindOneByCodeWithInvalidQueryParam(): void
    {
        $request = $this->createServerRequest();
        $request = $this->addValidAuthentication($request)
            ->withQueryParams(['foo' => 'boo-kee']);

        $response = self::$app->handle($request);

        $expectedResponse = $this->createJsonResponse(400, [
            'message' => 'Parameter `code` is required'
        ]);

        $this->assertResponseEquals($expectedResponse, $response);
    }
}
