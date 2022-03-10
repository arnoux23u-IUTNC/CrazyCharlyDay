<?php

namespace custombox\mvc\views;

use custombox\mvc\models\Produit;
use custombox\mvc\Renderer;
use custombox\mvc\View;
use JetBrains\PhpStorm\Pure;
use Slim\Container;
use Slim\Http\Request;

class ProductView extends View
{

    private ?Produit $product;

    #[Pure] public function __construct(Container $c, ?Produit $p, Request $request = null)
    {
        parent::__construct($c, $request);
        $this->product = $p;
    }

    public function render(int $method, int $access_level = Renderer::OTHER_MODE): string
    {
        return match ($method) {
            Renderer::SHOW_ALL => $this->all(),
            Renderer::SHOW_IN_LIST => $this->forList(),
            default => parent::render($method, $access_level),
        };
    }

    protected function forList(): string
    {
        return <<<HTML
            <div class="card">
                <div class="card-body">
                    <h1>{$this->product['id']}</h1>
                    <p>{$this->product['titre']}</p>
                    <p>{$this->product['description']}</p>
                    <p>{$this->product['categorie']}</p>
                    <p>{$this->product['poids']}</p>
                    <img src="/assets/images/produits/{$this->product['id']}.jpg" alt="{$this->product['description']}">
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    protected function show(): string
    {
        $html = <<<HTML
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1>{$this->product['id']}</h1>
                        <p>{$this->product['titre']}</p>
                        <p>{$this->product['description']}</p>
                        <p>{$this->product['categorie']}</p>
                        <p>{$this->product['poids']}</p>
                        <img src="/assets/images/produits/{$this->product['id']}" alt="{$this->product['description']}">
                    </div>
                </div>
            </div>
        </body>
        </html>
        HTML;
        return genererHeader("{$this->product['titre']}") . $html;
    }

    protected function all(): string
    {
        $html = genererHeader("Produits");
        $products = "";
        foreach (Produit::all() as $product) {
            $products .= (new ProductView($this->container, $product, $this->request))->render(Renderer::SHOW_IN_LIST);
        }
        $html = <<<HTML
            <div class="container">
                $products
            </div>
        </body>
        </html>
        HTML;
        return genererHeader("Produits") . $html;
    }
}