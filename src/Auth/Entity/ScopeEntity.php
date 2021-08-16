<?php

namespace Com\Pulunomoe\PuluAuth\Auth\Entity;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\ScopeTrait;

class ScopeEntity implements ScopeEntityInterface
{
	use EntityTrait;
	use ScopeTrait;

	public function __construct(String $identifier)
	{
		$this->identifier = $identifier;
	}

	public function getIdentifier()
	{
		return $this->identifier;
	}
}
