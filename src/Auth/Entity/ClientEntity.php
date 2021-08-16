<?php

namespace Com\Pulunomoe\PuluAuth\Auth\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
	use EntityTrait;
	use ClientTrait;
}
