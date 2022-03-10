<?php

namespace custombox\mvc\controllers;

use custombox\exceptions\ForbiddenException;
use custombox\mvc\models\Produit;
use custombox\mvc\models\User;
use custombox\mvc\Renderer;
use custombox\mvc\views\ProduitView;
use Slim\Container;
use Slim\Http\{Response, Request};
use Slim\Exception\{MethodNotAllowedException, NotFoundException};

class ControllerProduits
{

    private Container $container;
    private ProduitView $renderer;
    private ?Produit $product;
    private ?User $user;
    private Request $request;
    private Response $response;
    private array $args;

    public function __construct(Container $c, Request $request, Response $response, array $args)
    {
        $this->container = $c;
        $this->product = Produit::where("id", "LIKE", filter_var($args['id'] ?? "", FILTER_SANITIZE_NUMBER_INT))->first();
        $this->renderer = new ProduitView($this->container, $this->product, $request);
        $this->user = User::find($_SESSION['USER_ID'] ?? -1) ?? NULL;
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
    }

    /**
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function display(): Response
    {
        if (empty($this->product))
            throw new NotFoundException($this->request, $this->response);
        return $this->response->write($this->renderer->render(Renderer::SHOW));
    }

    /**
     * @throws ForbiddenException
     */
    public function displayAll(): Response
    {
        return $this->response->write($this->renderer->render(Renderer::SHOW_ALL));
    }

    public function create(): Response
    {
        switch ($this->request->getMethod()) {
            case 'GET':
                return $this->response->write($this->renderer->render(Renderer::CREATE));
            case 'POST':
                $nom = filter_var($this->request->getParsedBodyParam('name'), FILTER_SANITIZE_STRING);;
                $description = filter_var($this->request->getParsedBodyParam('desc'), FILTER_SANITIZE_STRING);;
                $categorie = filter_var($this->request->getParsedBodyParam('categ'), FILTER_SANITIZE_STRING);
                $poids = filter_var($this->request->getParsedBodyParam('poids'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);;
                $product = new Produit();
                $product->titre = $nom;
                $product->description = $description;
                $product->categorie = $categorie;
                $product->poids = $poids;
                $product->save();
                return $this->response->withRedirect($this->container['router']->pathFor('afficherProduits'));
            default :
                throw new MethodNotAllowedException($this->request, $this->response, ['GET', 'POST']);
        }
    }

    /**
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @throws ForbiddenException
     */
    public function edit(): Response
    {
        if ( !empty($this->user) && !$this->user->isAdmin())
            throw new ForbiddenException("Accès refusé");
        if (empty($this->product))
            throw new NotFoundException($this->request, $this->response);
        switch ($this->request->getMethod()) {
            case 'GET':
                return $this->response->write($this->renderer->render(Renderer::EDIT));
            case 'POST':
                $this->product->update([
                    //TODO
                    'titre' => filter_var($this->request->getParsedBodyParam('titre'), FILTER_SANITIZE_STRING),
                    'description' => filter_var($this->request->getParsedBodyParam('description'), FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                    'expiration' => $this->request->getParsedBodyParam('expiration') !== "" ? filter_var($this->request->getParsedBodyParam('expiration'), FILTER_SANITIZE_STRING) : NULL,
                    'public_key' => filter_var($this->request->getParsedBodyParam('public_key'), FILTER_SANITIZE_STRING),
                    'is_public' => filter_var($this->request->getParsedBodyParam('conf') ?? 0, FILTER_SANITIZE_NUMBER_INT),
                ]);
                return $this->response->withRedirect($this->container['router']->pathFor('afficherProduit', ["id" => $this->product->id]));
            default:
                throw new MethodNotAllowedException($this->request, $this->response, ['GET', 'POST']);
        }
    }

}