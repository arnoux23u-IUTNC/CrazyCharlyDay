<?php


//TODO

namespace custombox\mvc\views;

?>
<style>
<?php include 'main.css'; ?>
</style>
<div class="tableau">
<?php

use custombox\exceptions\ForbiddenException;
use custombox\mvc\models\Categorie;
use custombox\mvc\models\Produit;
use custombox\mvc\Renderer;
use custombox\mvc\View;
use JetBrains\PhpStorm\Pure;
use Slim\Container;
use Slim\Http\Request;

class ProduitView extends View
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
            Renderer::EDIT => $this->edit(),
            Renderer::SHOW_ALL => $this->all(),
            Renderer::SHOW_IN_LIST_MIN => $this->forList(true),
            Renderer::CREATE => $this->create(),
            default => parent::render($method, $access_level),
        };
    }

    protected function forList($min = false): string
    {
        return !$min ? <<<HTML
            <div class="card">
                <div class="card-body">
                    <div class="block">
                        <h1>{$this->product['titre']}</h1>
                        <p><b>Description</b> : {$this->product['description']}</p>
                        <p><b>Categorie</b> : {$this->product['categorie']}</p>
                        <p><b>Poids</b> : {$this->product['poids']} kg</p>
                    </div>
                    <img src="/assets/images/produits/{$this->product['id']}.jpg" alt="{$this->product['description']}">
                </div>
            </div>
        </body>
        </html>
        HTML : <<<HTML
            <div style=" display: flex; flex-direction: row; border:1px solid black; width: 33vw;">
                <div style="font-size: 10px;">
                    <p>{$this->product['titre']}</p>
                    <p><b>Description</b> : {$this->product['description']}</p>
                    <p><b>Categorie</b> : {$this->product['categorie']}</p>
                    <p><b>Poids</b> : {$this->product['poids']} kg</p>
                    <label style="font-size: 15px" for="{$this->product['id']}">Qte :</label>
                    <input value="0" type="number" min="0" name="{$this->product['id']}"  />
                </div>
                <img style="width: auto; height: 15vh; margin: 0 !important; border-radius: 0 !important;" src="/assets/images/produits/{$this->product['id']}.jpg" alt="{$this->product['description']}">
            </div>
        </body>
        </html>
        HTML;
    }

    protected function show(): string
    {
        $html = <<<HTML
        <div class="card">
            <div class="card-body">
                <div class="block">
                    <h1>{$this->product['titre']}</h1>
                    <p><b>Description</b> : {$this->product['description']}</p>
                    <p><b>Categorie</b> : {$this->product['categorie']}</p>
                    <p><b>Poids</b> : {$this->product['poids']} kg</p>
                </div>
                <img src="/assets/images/produits/{$this->product['id']}.jpg" alt="{$this->product['description']}">
            </div>
        </div>
    </body>
    </html>
    HTML;
        return genererHeader("{$this->product['titre']}") . $html;
    }

    /**
     * @throws ForbiddenException
     */
    protected function all(): string
    {
        $products = "";
        foreach (Produit::all() as $product) {
            $products .= (new ProduitView($this->container, $product, $this->request))->render(Renderer::SHOW_IN_LIST);
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
        $url = $this->container['router']->pathFor('creerProduit');
        $categorie = Categorie::all();
        $categ = "";
        foreach ($categorie as $c) {
            $categ .= "<option value=" . $c['id'] . ">" . $c['nom'] . "</option><br>";
        }
        $html = <<<HTML
            <form action='$url' method='POST'>
                <h2>Creer un nouveau produit</h2>
                <label>Nom du produit</label>
                <input type='text' name='name' placeholder='Ex: Gel Douche' required><br>
                
                <label>Entrez une description</label>
                <input type='text' name='desc' placeholder='Sert à se laver' required><br>
                
                <label>Selectionnez une catégorie</label>
                <select name="categ">
                $categ
                </select><br>
                
                <label>Entrez un poids</label>
                <input type='number' name='poids' value="0"  min="0" step="0.01" required><br>
                
                <button type='submit' name='submit' value='create'>Creer Item</button>
            </form>
        </body>
        </html>
        HTML;
        return genererHeader("Creer") . $html;
    }

    protected function edit(): string
    {
        $url = $this->container['router']->pathFor('modifierProduit', ['id'=>$this->product['id']]);
        $categorie = Categorie::all();
        $categ = "";
        foreach ($categorie as $c) {
            if ($this->product['categorie'] === $c ) {
                $categ .= "<option selected='selected' value={$c['id']} - {$c['nom']} </option><br>";
            }
            else {
                $categ .= "<option value=" . $c['id'] . ">" . $c['nom'] . "</option><br>";
            }

        }
        $html = <<<HTML
            <form action='$url' method='POST'>
                <h2>Modifier le produit</h2>
                <label>Nom du produit</label>
                <input type='text' name='name' placeholder='' value="{$this->product['titre']}" required><br>
                
                <label>Entrez une description</label>
                <input type='text' name='desc' placeholder='' value="{$this->product['description']}" required><br>
                
                <label>Selectionnez une catégorie</label>
                <select name="categ">
                $categ
                </select><br>
                
                <label>Entrez un poids</label>
                <input type='number' name='poids' value="{$this->product['poids']}"  min="0" step="0.01" required><br>
                
                <button type='submit' name='submit' value='create'>Modifier Item</button>
              
            </form>
        </body>
        </html>
        HTML;
        return genererHeader("Modifier produit") . $html;
    }
}
?>
</div>
<?php