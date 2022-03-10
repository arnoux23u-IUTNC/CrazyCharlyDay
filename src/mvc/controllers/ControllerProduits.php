<?php

namespace custombox\mvc\controllers;

use custombox\exceptions\ForbiddenException;
use custombox\mvc\models\Produit;
use custombox\mvc\Renderer;
use custombox\mvc\views\ProductView;
use Slim\Container;
use Slim\Http\{Response, Request};
use Slim\Exception\{NotFoundException};

class ControllerProduits
{

    private Container $container;
    private ProductView $renderer;
    private ?Produit $product;
    private Request $request;
    private Response $response;
    private array $args;

    public function __construct(Container $c, Request $request, Response $response, array $args)
    {
        $this->container = $c;
        $this->product = Produit::where("id", "LIKE", filter_var($args['id'] ?? "", FILTER_SANITIZE_NUMBER_INT))->first();
        $this->renderer = new ProductView($this->container, $this->product, $request);
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


}