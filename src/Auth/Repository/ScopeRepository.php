<?php

namespace Com\Pulunomoe\PuluAuth\Auth\Repository;

use Com\Pulunomoe\PuluAuth\Auth\Entity\ScopeEntity;
use Com\Pulunomoe\PuluAuth\Model\ScopeModel;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use PDO;

class ScopeRepository implements ScopeRepositoryInterface
{
	private ScopeModel $scopeModel;

	public function __construct(PDO $pdo)
	{
		$this->scopeModel = new ScopeModel($pdo);
	}

	public function getScopeEntityByIdentifier($identifier): ?ScopeEntity
	{
		$scope = $this->scopeModel->findOneByName($identifier);
		if (empty($scope)) return null;

		$scopeEntity = new ScopeEntity($identifier);
		return $scopeEntity;
	}

	public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null): array
	{
		$scopeEntities = [];

		foreach ($scopes as $scope) {
			if (empty($this->scopeModel->findOneByName($scope->getIdentifier()))) continue;
			$scopeEntities[] = $scope;
		}

		return $scopeEntities;
	}
}
