<?php

namespace custombox\mvc\views;

use custombox\exceptions\ForbiddenException;
use custombox\mvc\models\Commande;
use custombox\mvc\models\Produit;
use custombox\mvc\models\User;
use custombox\mvc\Renderer;
use custombox\mvc\View;
use custombox\QRGenerator;
use JetBrains\PhpStorm\Pure;
use Slim\Container;
use Slim\Http\Request;

class CommandeView extends View
{

    private ?Commande $command;

    #[Pure] public function __construct(Container $c, ?Commande $co, Request $request = null)
    {
        $this->command = $co;
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

    protected function show($b = false): string
    {
        $products = "";
        foreach ($this->command->produits as $produit) {
            $products .= <<<HTML
                <tr>
                    <th scope="row">{$produit['titre']}</th>
                    <td>{$produit->pivot->qte}</td>
                </tr>
            HTML;
        }
        $qr = !$b ? QRGenerator::generateQRCODE((string)$this->request->getUri()) : "";
        $html = <<<HTML
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1>Commande {$this->command['id']}</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h2>Produits</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                $products
                            </tbody>
                        </table>
                    </div>      
                </div>
                <div class="row">
                    <p style="background-color: {$this->command->couleur_boite}" >couleur boite</p>
                </div>
            </div>
            <div>
                $qr
            </div>
        </body>     
        </html>
        HTML;
        return !$b ? genererHeader("Commande {$this->command['id']}") . $html : $html;
    }

    /**
     * @throws ForbiddenException
     */
    protected function create(): string
    {
        $productsHtml = "";
        foreach (Produit::all() as $product) {
            $productsHtml .= (new ProduitView($this->container, $product, $this->request))->render(Renderer::SHOW_IN_LIST_MIN);
        }
        return genererHeader("Création de commande", ["commande.css"]) . <<<HTML
            <form method="POST" action="{$this->container['router']->pathFor('creerCommande')}">
                <h1>Créer une commande</h1>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr);">
                    $productsHtml
                </div>
                <label for="color">Couleur boite</label>
                <input type="color" name="color" id="color">
                <input type="submit" value="Créer">
            </form>
        HTML;
    }

    public function all(): string
    {
        $html = '';
        foreach (Commande::all() as $commande) {
            $html .= (new CommandeView($this->container, $commande, $this->request))->render(Renderer::SHOW_IN_LIST_MIN);
        }
        return genererHeader("Liste des Commandes") . <<<HTML
            <div>
                <h1>Liste des Commandes</h1>
                $html
            </div>
        </body>
        </html>
        HTML;

    }


    public function render(int $method, int $access_level = Renderer::OTHER_MODE, $user = null): string
    {
        return match ($method) {
            Renderer::CREATE => $this->create(),
            Renderer::SHOW_ALL => $this->all(),
            Renderer::SHOW_IN_LIST_MIN => $this->show(true),
            default => parent::render($method, $access_level),
        };
    }
}