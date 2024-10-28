<?php

namespace App\Mediator\Authentication;

use App\Application\Exception\AuthenticationException;
use App\Application\Exception\MediatorException;
use App\Application\Mediator\AuthenticationMediator;
use App\Domain\Administrator\Administrator;
use App\Domain\Administrator\AdministratorCode;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\Key;
use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface;

readonly class ApiKeyAuthenticationMediator implements AuthenticationMediator
{
    public function __construct(
        protected PDO $pdo,
        private Key $key,
        private int $maxRequestAge
    ) {
    }

    /**
     * @throws MediatorException
     * @throws AuthenticationException
     */
    public function authenticate(ServerRequestInterface $request): ServerRequestInterface
    {
        $apiKey = $request->getHeader('PULU-API-KEY')[0] ?? null;
        $timestamp = $request->getHeader('PULU-TIMESTAMP')[0] ?? null;
        $signature = $request->getHeader('PULU-SIGNATURE')[0] ?? null;

        if (!$apiKey || !$timestamp || !$signature || !is_numeric($timestamp)) {
            throw new AuthenticationException('Missing API key, signature, or timestamp');
        }

        if (time() - $timestamp > $this->maxRequestAge || $timestamp > time()) {
            throw new AuthenticationException('Request expired');
        }

        try {
            $stmt = $this->pdo->prepare('SELECT id, code, name, email, secret_key FROM administrators WHERE api_key = :apiKey LIMIT 1');
            $stmt->bindValue(':apiKey', $apiKey);
            $stmt->execute();
            $row = $stmt->fetch();
        } catch (PDOException $e) {
            throw new MediatorException($e);
        }

        if (!$row) {
            throw new AuthenticationException('Invalid API key');
        }

        try {
            $secretKey = Crypto::decrypt($row['secret_key'], $this->key);
            $expectedSignature = hash_hmac('sha256', $apiKey . $timestamp, $secretKey);
            if (!hash_equals($expectedSignature, $signature)) {
                throw new AuthenticationException('Invalid signature');
            }
        } catch (EnvironmentIsBrokenException|WrongKeyOrModifiedCiphertextException $e) {
            throw new MediatorException($e);
        }

        return $request->withAttribute('administrator', new Administrator(
            $row['id'],
            new AdministratorCode($row['code']),
            $row['name'],
            $row['email']
        ));
    }
}
