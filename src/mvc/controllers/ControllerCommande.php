<?php

namespace custombox\mvc\controllers;

use custombox\exceptions\ForbiddenException;
use custombox\mvc\models\Produit;
use custombox\mvc\Renderer;
use custombox\mvc\views\ProductView;
use Slim\Container;
use Slim\Http\{Response, Request};
use Slim\Exception\{MethodNotAllowedException, NotFoundException};

class ControllerCommande
{

    private Container $container;
    private CommandeView $renderer;
    private ?Commande $commande;
    private Request $request;
    private Response $response;
    private array $args;

    public function __construct(Container $c, Request $request, Response $response, array $args)
    {
        $this->container = $c;
        $this->commande = Commande::where("id", "LIKE", filter_var($args['id'] ?? "", FILTER_SANITIZE_NUMBER_INT))->first();
        $this->renderer = new CommandeView($this->container, $this->commande, $request);
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
        if (empty($this->commande))
            throw new NotFoundException($this->request, $this->response);
        return $this->response->write($this->renderer->render(Renderer::SHOW));
    }

    /**
     * @throws ForbiddenException
     */

    public function create(): Response
    {
        switch ($this->request->getMethod()){
            case 'GET':
                return $this->response->write($this->renderer->render(Renderer::CREATE));
            default :
                throw new MethodNotAllowedException($this->request,$this->response,['GET']);
        }
    }

    public function list():void
    {
        //TODO

    }

}