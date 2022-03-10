<?php

namespace custombox\mvc\controllers;

use custombox\exceptions\ForbiddenException;
use custombox\mvc\Renderer;
use custombox\mvc\views\UserView;
use JetBrains\PhpStorm\Pure;
use Slim\Container;
use Slim\Http\{Response, Request};
use Slim\Exception\{NotFoundException};

class ControllerUser
{

    private Container $container;
    private UserView $renderer;
    private Request $request;
    private Response $response;
    private array $args;

    #[Pure] public function __construct(Container $c, Request $request, Response $response, array $args)
    {
        $this->container = $c;
        $this->renderer = new UserView($this->container,  $request);
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
    }

    /**
     * @throws ForbiddenException
     */
    public function home(): Response
    {
        return $this->response->write($this->renderer->render(Renderer::HOME_HOME));
    }

    /**
     * @throws NotFoundException
     */
    public function process(): Response
    {
        return match ($this->args['action']) {
            default => throw new NotFoundException($this->request, $this->response),
        };
    }

}