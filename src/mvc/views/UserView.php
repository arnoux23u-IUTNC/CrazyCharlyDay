<?php

namespace custombox\mvc\views;

use custombox\mvc\Renderer;
use custombox\mvc\View;
use JetBrains\PhpStorm\Pure;
use Slim\Container;
use Slim\Http\Request;

class UserView extends View
{

    #[Pure] public function __construct(Container $c, Request $request = null)
    {
        parent::__construct($c, $request);
    }

    #[Pure] private function showHome(): string
    {
        $html = genererHeader("Accueil");
        $html .= <<<HTML
            BIENVENUE
        </body>
        </html>
        HTML;
        return $html;
    }

    public function render(int $method, int $access_level = Renderer::OTHER_MODE): string
    {
        return match ($method) {
            Renderer::HOME_HOME => $this->showHome(),
            default => parent::render($method, $access_level),
        };
    }

    protected function show()
    {
        // TODO: Implement show() method.
    }
}