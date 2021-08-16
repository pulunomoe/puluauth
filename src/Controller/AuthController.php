<?php

namespace Com\Pulunomoe\PuluAuth\Controller;

use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class AuthController
{
	private AuthorizationServer $authServer;

	public function __construct(AuthorizationServer $authServer)
	{
		$this->authServer = $authServer;
	}

	public function accessToken(ServerRequest $request, Response $response): ResponseInterface
	{
		return $this->authServer->respondToAccessTokenRequest($request, $response);
	}
}
