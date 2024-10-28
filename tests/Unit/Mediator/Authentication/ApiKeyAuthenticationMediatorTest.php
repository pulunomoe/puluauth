<?php

namespace Mediator\Authentication;

use App\Application\Exception\AuthenticationException;
use App\Application\Exception\MediatorException;
use App\Domain\Administrator\Administrator;
use App\Mediator\Authentication\ApiKeyAuthenticationMediator;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Key;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\PdoTestTrait;
use Tests\Psr7TestTrait;
use Tests\Unit\Domain\Administrator\AdministratorTest;

class ApiKeyAuthenticationMediatorTest extends TestCase
{
    use PdoTestTrait;
    use Psr7TestTrait;

    private PDOStatement&Stub $stmt;
    private PDO&Stub $pdo;

    protected ServerRequestInterface $request;
    protected ResponseInterface $response;

    private Key $key;
    private int $maxRequestAge = 300;
    private ApiKeyAuthenticationMediator $apiKeyAuthenticationAdapter;

    private Administrator $administrator;
    private string $apiKey = 'this-is-an-api-key';
    private string $secretKey = 'this-is-a-secret-key';

    public static function setUpBeforeClass(): void
    {
        self::setUpPsr7BeforeClass();
    }

    /**
     * @throws Exception
     * @throws EnvironmentIsBrokenException
     */
    protected function setUp(): void
    {
        $this->setUpPdo();

        $this->request = self::$psr17Factory->createServerRequest('GET', '/');

        $this->key = Key::createNewRandomKey();

        $this->apiKeyAuthenticationAdapter = new ApiKeyAuthenticationMediator(
            $this->pdo,
            $this->key,
            $this->maxRequestAge
        );

        $this->administrator = AdministratorTest::createAdministrator();
    }

    /**
     * @throws EnvironmentIsBrokenException
     */
    private function createValidDatabaseReturn(): array
    {
        return [
            'id' => $this->administrator->getId(),
            'code' => $this->administrator->getCode()->getValue(),
            'name' => $this->administrator->getName(),
            'email' => $this->administrator->getEmail(),
            'secret_key' => Crypto::encrypt($this->secretKey, $this->key)
        ];
    }

    private function calculateSignature(string $apiKey, int $timestamp): string
    {
        return hash_hmac('sha256', $apiKey . $timestamp, $this->secretKey);
    }

    private function createRequest(string $apiKey = null, string $timestamp = null, string $signature = null): ServerRequestInterface
    {
        $apiKey = $apiKey ?? $this->apiKey;
        $timestamp = $timestamp ?? time();
        $signature = $signature ?? $this->calculateSignature($apiKey, intval($timestamp));
        return $this->request
            ->withHeader('PULU-API-KEY', $apiKey)
            ->withHeader('PULU-TIMESTAMP', $timestamp)
            ->withHeader('PULU-SIGNATURE', $signature);
    }

    /**
     * @throws MediatorException
     * @throws AuthenticationException
     * @throws EnvironmentIsBrokenException
     */
    public function testAuthenticateSuccessful(): void
    {
        $this->stmt->method('fetch')
            ->willReturn($this->createValidDatabaseReturn());

        $result = $this->apiKeyAuthenticationAdapter->authenticate($this->createRequest());

        $expectedResult = $result->withAttribute('administrator', $this->administrator);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @throws MediatorException
     */
    public function testAuthenticateMiddlewareWithNoHeader(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Missing API key, signature, or timestamp');
        $this->apiKeyAuthenticationAdapter->authenticate($this->request);
    }

    /**
     * @throws MediatorException
     */
    public function testAuthenticateWithInvalidApiKey(): void
    {
        $this->stmt->method('fetch')
            ->willReturn(false);

        $request = $this->createRequest('invalid-api-key');

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid API key');
        $this->apiKeyAuthenticationAdapter->authenticate($request);
    }

    /**
     * @throws MediatorException
     */
    public function testAuthenticateWithNonNumericTimestamp(): void
    {
        $request = $this->createRequest(timestamp: 'not-a-number');

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Missing API key, signature, or timestamp');
        $this->apiKeyAuthenticationAdapter->authenticate($request);
    }

    /**
     * @throws MediatorException
     */
    public function testAuthenticateWithExpiredTimestamp(): void
    {
        $request = $this->createRequest(timestamp: time() - $this->maxRequestAge - 1);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Request expired');
        $this->apiKeyAuthenticationAdapter->authenticate($request);
    }

    /**
     * @throws MediatorException
     */
    public function testAuthenticateWithFutureTimestamp(): void
    {
        $request = $this->createRequest($this->apiKey, time() + 3600);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Request expired');
        $this->apiKeyAuthenticationAdapter->authenticate($request);
    }

    /**
     * @throws MediatorException
     * @throws EnvironmentIsBrokenException
     */
    public function testAuthenticateWithInvalidSignature(): void
    {
        $this->stmt->method('fetch')
            ->willReturn($this->createValidDatabaseReturn());

        $request = $this->createRequest(signature: 'invalid-signature');

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid signature');
        $this->apiKeyAuthenticationAdapter->authenticate($request);
    }

    /**
     * @throws AuthenticationException
     */
    public function testAuthenticateWithPdoException(): void
    {
        $this->stmt->method('fetch')
            ->willThrowException(new PDOException());

        $this->expectException(MediatorException::class);
        $this->apiKeyAuthenticationAdapter->authenticate($this->createRequest());
    }

    /**
     * @throws AuthenticationException
     */
    public function testAuthenticateWithEncryptionException(): void
    {
        $this->stmt->method('fetch')
            ->willReturn([
                'id' => $this->administrator->getId(),
                'code' => $this->administrator->getCode()->getValue(),
                'name' => $this->administrator->getName(),
                'email' => $this->administrator->getEmail(),
                'secret_key' => 'invalid-secret-key'
            ]);

        $this->expectException(MediatorException::class);
        $this->apiKeyAuthenticationAdapter->authenticate($this->createRequest());
    }
}
