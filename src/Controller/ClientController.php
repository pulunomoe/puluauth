<?php

namespace Com\Pulunomoe\PuluAuth\Controller;

use Com\Pulunomoe\PuluAuth\Model\ClientModel;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class ClientController extends Controller
{
	private ClientModel $clientModel;

	public function __construct(PDO $pdo)
	{
		$this->clientModel = new ClientModel($pdo);
	}

	public function index(ServerRequest $request, Response $response): ResponseInterface
	{
		return $this->render($request, $response, 'clients/index.twig', [
			'clients' => $this->clientModel->findAll(),
			'success' => $this->getFlash('success')
		]);
	}

	public function view(ServerRequest $request, Response $response, array $args): ResponseInterface
	{
		$clientId = $args['clientId'];

		return $this->render($request, $response, 'clients/view.twig', [
			'client' => $this->findOneOr404($request, $this->clientModel, $clientId),
			'success' => $this->getFlash('success'),
			'clientSecret' => $this->getFlash('clientSecret')
		]);
	}

	public function form(ServerRequest $request, Response $response, array $args): ResponseInterface
	{
		$clientId = $args['clientId'] ?? null;

		if (!empty($clientId)) {
			$client = $this->findOneOr404($request, $this->clientModel, $clientId);
		}

		return $this->render($request, $response, 'clients/form.twig', [
			'client' => $client ?? null,
			'csrf' => $this->generateCsrfToken(),
			'errors' => $this->getFlash('errors')
		]);
	}

	public function formPost(ServerRequest $request, Response $response): ResponseInterface
	{
		$this->verifyCsrfToken($request);

		$clientId = $request->getParam('id');
		$name = $request->getParam('name');
		$description = $request->getParam('description');

		$errors = $this->clientModel->validate($clientId, $name);
		if (!empty($errors)) {
			$this->setFlash('errors', $errors);
			$url = empty($clientId) ? '/clients/form' : '/clients/form/'.$clientId;
			return $response->withRedirect($url);
		}

		if (empty($clientId)) {
			$client = $this->clientModel->create($name, $description);
			$this->setFlash('clientSecret', $client['secret']);
			$this->setFlash('success', 'Client has been successfully created');
			return $response->withRedirect('/clients/view/'.$client['id']);
		} else {
			$this->clientModel->update($clientId, $name, $description);
			$this->setFlash('success', 'Client has been successfully updated');
			return $response->withRedirect('/clients/view/'.$clientId);
		}
	}

	public function newSecret(ServerRequest $request, Response $response, array $args): ResponseInterface
	{
		$clientId = $args['clientId'];

		return $this->render($request, $response, 'clients/newSecret.twig', [
			'client' => $this->findOneOr404($request, $this->clientModel, $clientId),
			'csrf' => $this->generateCsrfToken()
		]);
	}

	public function newSecretPost(ServerRequest $request, Response $response): ResponseInterface
	{
		$this->verifyCsrfToken($request);

		$clientId = $request->getParam('id');
		$this->findOneOr404($request, $this->clientModel, $clientId);

		$this->setFlash('clientSecret', $this->clientModel->newSecret($clientId));

		$this->setFlash('success', 'Client has been successfully updated');
		return $response->withRedirect('/clients/view/'.$clientId);
	}

	public function delete(ServerRequest $request, Response $response, array $args): ResponseInterface
	{
		$clientId = $args['clientId'];

		return $this->render($request, $response, 'clients/delete.twig', [
			'client' => $this->findOneOr404($request, $this->clientModel, $clientId),
			'csrf' => $this->generateCsrfToken(),
			'errors' => $this->getFlash('errors')
		]);
	}

	public function deletePost(ServerRequest $request, Response $response): ResponseInterface
	{
		$this->verifyCsrfToken($request);

		$clientId = $request->getParam('id');
		$this->findOneOr404($request, $this->clientModel, $clientId);

		$this->clientModel->delete($clientId);

		$this->setFlash('success', 'Client has been successfully deleted');
		return $response->withRedirect('/clients');
	}
}
