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
            Renderer::CREATE => $this->create(),
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
                        <img src="/assets/images/produits/{$this->product['id']}.jpg" alt="{$this->product['description']}">
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

    protected function create(): string
    {
        $url = $this->container->router->pathFor('creerProduit');
        $html = <<<HTML
            <form action='$url' method='POST'>
			    <h2>Creer un nouveau produit</h2>
			    			    
			    <label>Nom du produit</label>
			    <input type='text' name='name' placeholder='Ex: Gel Douche' required><br>
			    
			    <label>Entrez une description</label>
			    <input type='text' name='desc' placeholder='Sert à se laver' required><br>
			
			
			    <label>Choisissez une catégorie</label>
			    <input type='number' name='categ' value="0" min="0" step="1"  required><br>
			
			    <label>Entrez un poids</label>
			    <input type='number' name='poids' value="0"  min="0" step="0.01" required><br>
			
			    <button type='submit' name='submit' value='create'>Creer Item</button>
			</form>
        </body>
        </html>
HTML;

        return genererHeader("Creer") . $html;
    }
}