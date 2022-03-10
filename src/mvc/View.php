<?php

namespace custombox\mvc;

use custombox\exceptions\ForbiddenException;
use Slim\Container;
use Slim\Http\Request;

abstract class View
{

    protected Request $request;
    protected Container $container;
    protected int $access_level;

    public function __construct(Container $c, Request $request = null)
    {
        $this->container = $c;
        $this->request = $request;
    }

    protected abstract function show();

    //protected abstract function edit();

    /**
     * @throws ForbiddenException
     */
    public function render(int $method, int $access_level = Renderer::OTHER_MODE): string
    {
        $this->access_level = $access_level;
        return match ($method) {
            Renderer::SHOW => $this->show(),
            //Renderer::EDIT => $this->edit(),
            default => throw new ForbiddenException(message: "Access denied")
        };
    }

}