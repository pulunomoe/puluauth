<?php

namespace Com\Pulunomoe\PuluAuth\Controller;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
		try {
			$response = $this->authServer->respondToAccessTokenRequest($request, $response);
		} catch (OAuthServerException $e) {
			$response->write($e->getErrorType());
			$response->write(': ');
			$response->write($e->getMessage());
			return $response->withStatus(400);
		}

		return $response;
	}
}
