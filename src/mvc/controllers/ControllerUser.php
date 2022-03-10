<?php

namespace custombox\mvc\controllers;

use custombox\exceptions\ForbiddenException;
use custombox\mvc\models\User;
use custombox\mvc\Renderer;
use custombox\mvc\views\UserView;
use custombox\Validator;
use JetBrains\PhpStorm\Pure;
use Slim\Container;
use Slim\Http\{Response, Request};
use Slim\Exception\{MethodNotAllowedException, NotFoundException};

class ControllerUser
{

    private Container $container;
    private UserView $renderer;
    private ?User $user;
    private Request $request;
    private Response $response;
    private array $args;

    #[Pure] public function __construct(Container $c, Request $request, Response $response, array $args)
    {
        $this->container = $c;
        $this->user = User::find($_SESSION['USER_ID'] ?? -1) ?? NULL;
        $this->renderer = new UserView($this->container, $this->user, $request);
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
    }

    /**
     * @throws ForbiddenException
     */
    public function home(): Response
    {
        return $this->response->write($this->renderer->render(Renderer::HOME_HOME));
    }

    private function logout(): Response
    {
        if (empty($_SESSION['LOGGED_IN']))
            return $this->response->withRedirect($this->container['router']->pathFor('home'));
        User::logout();
        return match ($this->request->getQueryParam('info')) {
            'pc' => $this->response->withRedirect($this->container['router']->pathFor('accounts', ["action" => 'login'], ["info" => "pc"])),
            default => $this->response->withRedirect($this->container['router']->pathFor('home')),
        };
    }

    /**
     * @throws ForbiddenException
     * @throws MethodNotAllowedException
     */
    private function login(): Response
    {
        switch ($this->request->getMethod()) {
            case 'GET':
                if (!empty($_SESSION['LOGGED_IN']))
                    return $this->profile();
                else
                    return $this->response->write($this->renderer->render(Renderer::LOGIN));
            case 'POST':
                if ($this->request->getParsedBodyParam('sendBtn') !== "OK")
                    throw new ForbiddenException(message: "Invalid request");
                $username = filter_var($this->request->getParsedBodyParam('username'), FILTER_SANITIZE_STRING);
                $password = filter_var($this->request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING);
                $user = User::whereUsername($username)->first();
                if (empty($user))
                    return $this->response->withRedirect($this->container['router']->pathFor('accounts', ["action" => 'login'], ["info" => "nouser"]));
                if (!password_verify($password, $user->password))
                    return $this->response->withRedirect($this->container['router']->pathFor('accounts', ["action" => 'login'], ["info" => "password"]));
                $user->authenticate();
                return $this->response->withRedirect($this->container['router']->pathFor('home'));
            default:
                throw new MethodNotAllowedException($this->request, $this->response, ['GET', 'POST']);
        }
    }

    /**
     * @throws MethodNotAllowedException
     * @throws ForbiddenException
     */
    private function profile(): Response
    {
        if (empty($_SESSION['LOGGED_IN']))
            return $this->login();
        if (empty($this->user))
            throw new ForbiddenException(message: "Acces interdit");
        switch ($this->request->getMethod()) {
            case 'GET':
                if ( !empty($this->user) && $this->user->isAdmin())
                    return $this->response->write($this->renderer->render(Renderer::SHOW, Renderer::ADMIN_MODE));
                return $this->response->write($this->renderer->render(Renderer::SHOW));
            case 'POST':
                if (!password_verify(filter_var($this->request->getParsedBodyParam("input-old-password"), FILTER_SANITIZE_STRING), $this->user->password))
                    return $this->response->withRedirect($this->container['router']->pathFor('accounts', ["action" => 'profile'], ["info" => "password"]));
                $toUpdate = array();
                $i = 0;
                if ($this->request->getParsedBodyParam("input-email") !== $this->user->mail && filter_var($this->request->getParsedBodyParam("input-email"), FILTER_VALIDATE_EMAIL)) {
                    $toUpdate["mail"] = filter_var($this->request->getParsedBodyParam("input-email"), FILTER_SANITIZE_EMAIL);
                    $i++;
                }
                if ($this->request->getParsedBodyParam("input-first-name") !== $this->user->firstname) {
                    $toUpdate["firstname"] = filter_var($this->request->getParsedBodyParam("input-first-name"), FILTER_SANITIZE_STRING);
                    $i++;
                }
                if ($this->request->getParsedBodyParam("input-last-name") !== $this->user->lastname) {
                    $toUpdate["lastname"] = filter_var($this->request->getParsedBodyParam("input-last-name"), FILTER_SANITIZE_STRING);
                    $i++;
                }
                if ($this->request->getParsedBodyParam("input-phone") !== $this->user->phone) {
                    $toUpdate["phone"] = filter_var($this->request->getParsedBodyParam("input-phone"), FILTER_SANITIZE_STRING);
                    $i++;
                }
                if (Validator::validatePassword($this->request->getParsedBodyParam("input-new-password"), $this->request->getParsedBodyParam("input-new-password-c"))) {
                    $pwd = filter_var($this->request->getParsedBodyParam("input-new-password"), FILTER_SANITIZE_STRING);
                    if (password_verify($pwd, $this->user->password))
                        return $this->response->withRedirect($this->container['router']->pathFor('accounts', ["action" => 'profile'], ["info" => "equals"]));
                    $toUpdate["password"] = password_hash($pwd, PASSWORD_DEFAULT, ['cost' => 12]);
                    $i++;
                }
                if ($i > 0) {
                    $toUpdate["updated"] = date("Y-m-d H:i:s");
                    $this->user->update($toUpdate);
                    if (!empty($toUpdate["password"]))
                        return $this->response->withRedirect($this->container['router']->pathFor('accounts', ["action" => 'logout'], ["info" => "pc"]));
                    return $this->response->withRedirect($this->container['router']->pathFor('accounts', ["action" => 'profile'], ["info" => "success"]));
                }
                return $this->response->withRedirect($this->container['router']->pathFor('accounts', ["action" => 'profile'], ["info" => "no-change"]));
            default:
                throw new MethodNotAllowedException($this->request, $this->response, ['GET', 'POST']);
        }
    }

    /**
     * @throws MethodNotAllowedException
     * @throws ForbiddenException
     */
    private function register(): Response
    {
        if (!empty($_SESSION['LOGGED_IN']))
            return $this->profile();
        switch ($this->request->getMethod()) {
            case 'GET':
                return $this->response->write($this->renderer->render(Renderer::REGISTER));
            case 'POST':
                if ($this->request->getParsedBodyParam('sendBtn') !== "OK")
                    throw new ForbiddenException(message: "Acces interdit");
                $username = filter_var($this->request->getParsedBodyParam('username'), FILTER_SANITIZE_STRING) ?? NULL;
                $lastname = filter_var($this->request->getParsedBodyParam('lastname'), FILTER_SANITIZE_STRING) ?? NULL;
                $firstname = filter_var($this->request->getParsedBodyParam('firstname'), FILTER_SANITIZE_STRING) ?? NULL;
                $phone = filter_var($this->request->getParsedBodyParam('phone'), FILTER_SANITIZE_STRING) ?? NULL;
                $email = filter_var($this->request->getParsedBodyParam('email'), FILTER_VALIDATE_EMAIL) ? (filter_var($this->request->getParsedBodyParam('email'), FILTER_SANITIZE_EMAIL) ?? NULL) : NULL;
                $password = filter_var($this->request->getParsedBodyParam('password'), FILTER_SANITIZE_STRING) ?? NULL;
                $password_confirm = filter_var($this->request->getParsedBodyParam('password-confirm'), FILTER_SANITIZE_STRING) ?? NULL;
                if (!Validator::validateStrings([$username, $lastname, $firstname, $email, $password, $password_confirm]))
                    return $this->response->withRedirect($this->container['router']->pathFor('accounts', ["action" => 'register'], ["info" => "invalid"]));
                if (!Validator::validatePassword($password, $password_confirm))
                    return $this->response->withRedirect($this->container['router']->pathFor('accounts', ["action" => 'register'], ["info" => "password"]));
                $user = new User();
                $user['user_id'] = NULL;
                $user['username'] = $username;
                $user['lastname'] = $lastname;
                $user['firstname'] = $firstname;
                $user['phone'] = $phone;
                $user['mail'] = $email;
                $user['password'] = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
                $user['created_at'] = date("Y-m-d H:i:s");
                $user->save();
                $user->authenticate();
                return $this->response->withRedirect($this->container['router']->pathFor('home'));
            default:
                throw new MethodNotAllowedException($this->request, $this->response, ['GET', 'POST']);
        }
    }

    /**
     * @throws MethodNotAllowedException
     * @throws ForbiddenException
     */
    public function switchAdmin(): Response{
        if(!$this->request->getMethod() == 'POST')
            throw new MethodNotAllowedException($this->request, $this->response, ['POST']);
        if( empty($this->user) || !$this->user->isAdmin())
            throw new ForbiddenException(message: "Acces interdit");
        $user_id = filter_var($this->args['id'], FILTER_VALIDATE_INT) ?? NULL;
        if(!$user_id)
            throw new ForbiddenException(message: "Utilisateur introuvable");
        $user = User::find($user_id);
        if(!$user)
            throw new ForbiddenException(message: "Utilisateur introuvable");
        $user['is_admin'] = !$user['is_admin'];
        $user->save();
        return $this->response->withRedirect($this->container['router']->pathFor('usersList'));
    }

    /**
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @throws ForbiddenException
     */
    public function process(): Response
    {
        return match ($this->args['action']) {
            'login' => $this->login(),
            'profile' => $this->profile(),
            //'delete' => $this->delete(),
            'logout' => $this->logout(),
            'register' => $this->register(),
            default => throw new NotFoundException($this->request, $this->response),
        };
    }

    /**
     * @throws ForbiddenException
     */
    public function list(): Response
    {
        if ( empty($this->user) || !$this->user->isAdmin()) {
            throw new ForbiddenException(message: "Acces interdit");
        }
        return $this->response->write($this->renderer->render(Renderer::SHOW_ALL));
    }

}