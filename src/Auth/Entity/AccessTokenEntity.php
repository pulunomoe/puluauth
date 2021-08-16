<?php

namespace Com\Pulunomoe\PuluAuth\Auth\Entity;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

class AccessTokenEntity implements AccessTokenEntityInterface
{
	use EntityTrait;
	use TokenEntityTrait;
	use AccessTokenTrait;
}
