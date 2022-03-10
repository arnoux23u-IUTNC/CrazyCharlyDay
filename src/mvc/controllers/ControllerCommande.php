<?php

namespace custombox\mvc\controllers;

use custombox\exceptions\ForbiddenException;
use custombox\mvc\models\Boite;
use custombox\mvc\models\Commande;
use custombox\mvc\models\Produit;
use custombox\mvc\models\User;
use custombox\mvc\Renderer;
use custombox\mvc\views\CommandeView;
use custombox\mvc\views\ProductView;
use Slim\Container;
use Slim\Http\{Response, Request};
use Slim\Exception\{MethodNotAllowedException, NotFoundException};

class ControllerCommande
{

    private Container $container;
    private CommandeView $renderer;
    private ?Commande $commande;
    private ?User $user;
    private Request $request;
    private Response $response;
    private array $args;

    public function __construct(Container $c, Request $request, Response $response, array $args)
    {
        $this->container = $c;
        $this->commande = Commande::where("id", "LIKE", filter_var($args['id'] ?? "", FILTER_SANITIZE_NUMBER_INT))->first();
        $this->renderer = new CommandeView($this->container, $this->commande, $request);
        $this->user = User::find($_SESSION['USER_ID'] ?? -1) ?? NULL;
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
        if (empty($this->commande))
            throw new NotFoundException($this->request, $this->response);
        return $this->response->write($this->renderer->render(Renderer::SHOW));
    }

    /**
     * @throws ForbiddenException
     * @throws MethodNotAllowedException
     */

    public function create(): Response
    {
        if (empty($this->user))
            throw new ForbiddenException("Vous devez être connecté pour accéder à cette page");
        switch ($this->request->getMethod()) {
            case 'GET':
                return $this->response->write($this->renderer->render(Renderer::CREATE));
            case 'POST':
                $pT = 0;
                $commande = new Commande();
                foreach ($this->request->getParsedBody() as $key => $value) {
                    $prod = Produit::where("id", "LIKE", filter_var($key, FILTER_SANITIZE_NUMBER_INT))->first();
                    $pT += $prod['poids'] * filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                }
                $boite = Boite::orderBy('poidsmax', 'ASC')->where("poidsmax", ">=", $pT)->first()['id'];
                if(empty($boite))
                    throw new ForbiddenException("Impossible, aucune boite ne correspond à votre commande");
                $commande->id_user = $this->user['user_id'];
                $commande->id_boite = $boite;
                $commande->couleur_boite = filter_var($this->request->getParsedBody()['color'] ?? "#000000", FILTER_SANITIZE_STRING);
                $commande->paye = false;
                $commande->save();
                foreach ($this->request->getParsedBody() as $key => $value) {
                    $prod = Produit::where("id", "LIKE", filter_var($key, FILTER_SANITIZE_NUMBER_INT))->first();
                    $qte = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    if ($qte > 0)
                        $commande->produits()->attach($prod['id'], ['id_commande' => $commande['id'], 'id_produit' => filter_var($key, FILTER_SANITIZE_NUMBER_INT), 'qte' => $qte]);
                }
                return $this->response->withRedirect($this->container->router->pathFor('commande', ['id' => $commande['id']]));
            default :
                throw new MethodNotAllowedException($this->request, $this->response, ['GET', 'POST']);
        }
    }

    public function list(): Response
    {
        if(empty($this->user)){
            throw new ForbiddenException("Vous n'etes pas connecté");
        }
        return $this->response->write($this->renderer->render(Renderer::SHOW_ALL));

    }

    /**
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function show():Response
    {
        if (empty($this->commande))
            throw new NotFoundException($this->request, $this->response);
        return $this->response->write($this->renderer->render(Renderer::SHOW));
    }

}