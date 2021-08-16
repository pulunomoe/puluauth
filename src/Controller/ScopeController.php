<?php

namespace Com\Pulunomoe\PuluAuth\Controller;

use Com\Pulunomoe\PuluAuth\Model\ScopeModel;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class ScopeController extends Controller
{
	private ScopeModel $scopeModel;

	public function __construct(PDO $pdo)
	{
		$this->scopeModel = new ScopeModel($pdo);
	}

	public function index(ServerRequest $request, Response $response): ResponseInterface
	{
		return $this->render($request, $response, 'scopes/index.twig', [
			'scopes' => $this->scopeModel->findAll(),
			'success' => $this->getFlash('success')
		]);
	}

	public function view(ServerRequest $request, Response $response, array $args): ResponseInterface
	{
		$scopeId = $args['scopeId'];

		return $this->render($request, $response, 'scopes/view.twig', [
			'scope' => $this->findOneOr404($request, $this->scopeModel, $scopeId),
			'success' => $this->getFlash('success'),
			'scopeSecret' => $this->getFlash('scopeSecret')
		]);
	}

	public function form(ServerRequest $request, Response $response, array $args): ResponseInterface
	{
		$scopeId = $args['scopeId'] ?? null;

		if (!empty($scopeId)) {
			$scope = $this->findOneOr404($request, $this->scopeModel, $scopeId);
		}

		return $this->render($request, $response, 'scopes/form.twig', [
			'scope' => $scope ?? null,
			'csrf' => $this->generateCsrfToken(),
			'errors' => $this->getFlash('errors')
		]);
	}

	public function formPost(ServerRequest $request, Response $response): ResponseInterface
	{
		$this->verifyCsrfToken($request);

		$scopeId = $request->getParam('id');
		$name = $request->getParam('name');
		$description = $request->getParam('description');

		$errors = $this->scopeModel->validate($scopeId, $name);
		if (!empty($errors)) {
			$this->setFlash('errors', $errors);
			$url = empty($scopeId) ? '/admin/scopes/form' : '/admin/scopes/form/'.$scopeId;
			return $response->withRedirect($url);
		}

		if (empty($scopeId)) {
			$scopeId = $this->scopeModel->create($name, $description);
			$this->setFlash('success', 'Scope has been successfully created');
		} else {
			$this->scopeModel->update($scopeId, $name, $description);
			$this->setFlash('success', 'Scope has been successfully updated');
		}

		return $response->withRedirect('/admin/scopes/view/'.$scopeId);

	}

	public function delete(ServerRequest $request, Response $response, array $args): ResponseInterface
	{
		$scopeId = $args['scopeId'];

		return $this->render($request, $response, 'scopes/delete.twig', [
			'scope' => $this->findOneOr404($request, $this->scopeModel, $scopeId),
			'csrf' => $this->generateCsrfToken(),
			'errors' => $this->getFlash('errors')
		]);
	}

	public function deletePost(ServerRequest $request, Response $response): ResponseInterface
	{
		$this->verifyCsrfToken($request);

		$scopeId = $request->getParam('id');
		$this->findOneOr404($request, $this->scopeModel, $scopeId);

		$this->scopeModel->delete($scopeId);

		$this->setFlash('success', 'Scope has been successfully deleted');
		return $response->withRedirect('/admin/scopes');
	}
}
