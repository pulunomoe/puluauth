<?php

namespace Com\Pulunomoe\PuluAuth\Auth\Repository;

use Com\Pulunomoe\PuluAuth\Auth\Entity\AccessTokenEntity;
use Com\Pulunomoe\PuluAuth\Model\TokenModel;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use PDO;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
	private TokenModel $tokenModel;

	public function __construct(PDO $pdo)
	{
		$this->tokenModel = new TokenModel($pdo);
	}

	public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntity
	{
		$accessTokenEntity = new AccessTokenEntity();
		$accessTokenEntity->setClient($clientEntity);
		$accessTokenEntity->setUserIdentifier($userIdentifier);

		foreach ($scopes as $scope) {
			$accessTokenEntity->addScope($scope);
		}

		return $accessTokenEntity;
	}

	public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
	{
		$this->tokenModel->create(
			$accessTokenEntity->getClient()->getIdentifier(),
			$accessTokenEntity->getIdentifier(),
			$accessTokenEntity->getExpiryDateTime(),
			$accessTokenEntity->getScopes()
		);
	}

	public function revokeAccessToken($tokenId)
	{
		$this->tokenModel->delete($tokenId);
	}

	public function isAccessTokenRevoked($tokenId): bool
	{
		return $this->tokenModel->findOneByIdentider($tokenId);
	}
}
