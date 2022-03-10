<?php

namespace custombox\mvc\views;

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
			    <h2>Creer une nouvelle boite</h2>
			    			    
			    <label>Taille de la boite</label>
			    <input type='text' name='taille' placeholder='Ex: Géant' required><br>
			    <label>Entrez un poids</label>
			    <input type='number' name='poids' value="0"  min="0" step="0.01" required><br>
			
			    <button type='submit' name='submit' value='create'>Creer Boite</button>
			</form>
        </body>
        </html>
HTML;

        return genererHeader("Creer") . $html;
    }
}