<?php

namespace custombox\mvc\controllers;

use custombox\exceptions\ForbiddenException;
use custombox\mvc\models\Boite;
use custombox\mvc\models\Produit;
use custombox\mvc\models\User;
use custombox\mvc\Renderer;
use custombox\mvc\views\BoiteView;
use custombox\mvc\views\ProduitView;
use Slim\Container;
use Slim\Http\{Response, Request};
use Slim\Exception\{MethodNotAllowedException, NotFoundException};

class ControllerBoite
{

    private Container $container;
    private BoiteView $renderer;
    private ?Boite $boite;
    private ?User $user;
    private Request $request;
    private Response $response;
    private array $args;

    public function __construct(Container $c, Request $request, Response $response, array $args)
    {
        $this->container = $c;
        $this->boite = Boite::where("id", "LIKE", filter_var($args['id'] ?? "", FILTER_SANITIZE_NUMBER_INT))->first();
        $this->renderer = new BoiteView($this->container, $this->boite, $request);
        $this->user = User::find($_SESSION['USER_ID'] ?? "-1") ?? NULL;
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
        if (empty($this->boite))
            throw new NotFoundException($this->request, $this->response);
        return $this->response->write($this->renderer->render(Renderer::SHOW));
    }

    /**
     * @throws ForbiddenException
     */
    public function displayAll(): Response
    {
        return $this->response->write($this->renderer->render(Renderer::SHOW_ALL));
    }

    /**
     * @throws ForbiddenException
     * @throws MethodNotAllowedException
     */
    public function create(): Response
    {
        if(!$this->user->isAdmin())
            throw new ForbiddenException("Vous n'avez pas les droits pour créer une boite");
        switch ($this->request->getMethod()){
            case 'GET':
                return $this->response->write($this->renderer->render(Renderer::CREATE));
            case 'POST':
                $taille = filter_var($this->request->getParsedBodyParam('taille'), FILTER_SANITIZE_STRING);;
                $poidsmax= filter_var($this->request->getParsedBodyParam('poids'),FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);;
                $boite = new Boite();
                $boite->taille = $taille;
                $boite->poidsmax = $poidsmax;
                $boite->save();
                return $this->response->withRedirect($this->container->router->pathFor('afficherBoites'));
            default :
                throw new MethodNotAllowedException($this->request,$this->response,['GET','POST']);
        }
    }

}
