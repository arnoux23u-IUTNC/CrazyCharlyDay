<?php

namespace custombox\mvc\views;

?>
<style>
<?php include '../../../assets/css/modifierProduit.css'; ?>
</style>
<div class="tableau">
<?php

use custombox\mvc\models\Boite;
use custombox\mvc\Renderer;
use custombox\mvc\View;
use JetBrains\PhpStorm\Pure;
use Slim\Container;
use Slim\Http\Request;

class BoiteView extends View
{

    private ?Boite $boite;

    #[Pure] public function __construct(Container $c, ?Boite $b, Request $request = null)
    {
        parent::__construct($c, $request);
        $this->boite = $b;
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
                    <h1>{$this->boite['id']}</h1>
                    <p>{$this->boite['taille']}</p>
                    <p>{$this->boite['poidsmax']}</p>
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
                        <h1>{$this->boite['id']}</h1>
                        <p>{$this->boite['taille']}</p>
                        <p>{$this->boite['poidsmax']}</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        HTML;
        return genererHeader("{$this->boite['taille']}") . $html;
    }

    protected function all(): string
    {
        $html = genererHeader("Boites");
        $boites = "";
        foreach (Boite::all() as $boite) {
            $boites .= (new BoiteView($this->container, $boite, $this->request))->render(Renderer::SHOW_IN_LIST);
        }
        $html = <<<HTML
            <div class="container">
                $boites
            </div>
        </body>
        </html>
        HTML;
        return genererHeader("Boites") . $html;
    }

    protected function create(): string
    {
        $url = $this->container->router->pathFor('creerBoite');
        $html = <<<HTML
            <form action='$url' method='POST'>
                <div class="modifieur">
                    <p>Modifier le produit</p>
                    <label>Nom du produit</label>
                    <input type='text' name='name' placeholder='' value="{$this->product['titre']}" required><br><br>
                    <label>Entrez une description</label>
                    <input type='text' name='desc' placeholder='' value="{$this->product['description']}" required><br><br>
                    <label>Selectionnez une catégorie</label>
                    <select name="categ">
                    $categ
                    </select><br><br>
                    <label>Entrez un poids</label>
                    <input type='number' name='poids' value="{$this->product['poids']}"  min="0" step="0.01" required><br><br>
                    <button type='submit' name='submit' value='create'>Modifier Item</button>
                </div>
            </form>
        </body>
        </html>
HTML;

        return genererHeader("Creer") . $html;
    }

    protected function edit(): string
    {
        $url = $this->container['router']->pathFor('modifierBoite');
        $html = <<<HTML
            <form action='$url' method='POST'>
			    <h2>Modifier la boite</h2>
			    			    
			    <label>Taille de la boite</label>
			    <input type='text' name='taille' placeholder='' value="$this->boite[taille]" required><br>
			    <label>Entrez un poids</label>
			    <input type='number' name='poids' value="$this->boite[poidsmax]"  min="0" step="0.01" required><br>
			
			    <button type='submit' name='submit' value='create'>Modifier Boite</button>
			</form>
        </body>
        </html>
HTML;
        return genererHeader("Creer") . $html;
    }

}