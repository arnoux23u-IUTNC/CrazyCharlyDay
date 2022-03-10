<?php

namespace custombox\mvc\views;

use custombox\mvc\models\User;
use custombox\mvc\Renderer;
use custombox\mvc\View;
use JetBrains\PhpStorm\Pure;
use Slim\Container;
use Slim\Http\Request;

class UserView extends View
{

    private ?User $user;

    #[Pure] public function __construct(Container $c, ?User $user, Request $request = null)
    {
        $this->user = $user;
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


    #[Pure] protected function show(): string
    {
        $html = genererHeader("Profil", ["profile.css"]) . file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'profile.phtml');
        $user = $this->user;
        $phtmlVars = array(
            "main_route" => $this->container['router']->pathFor('home'),
            "profile_route" => $this->container['router']->pathFor('accounts', ["action" => 'profile']),
            "logout_route" => $this->container['router']->pathFor('accounts', ["action" => 'logout']),
            "delete_account_route" => $this->container['router']->pathFor('accounts', ["action" => 'delete']),
            "user_username" => $user['username'],
            "user_firstname" => $user['firstname'],
            "user_lastname" => $user['lastname'],
            "user_email" => $user['mail'],
            "user_phone" => $user['phone'],
            "user_created_at" => $user['created_at'],
            "user_updated_at" => $user['updated'] ?? "Jamais",
            "user_lastlogged_at" => $user['last_login'],
            "user_lastlogged_ip" => $user['last_ip'],
            "info_msg" => match (filter_var($this->request->getQueryParam('info'), FILTER_SANITIZE_STRING) ?? "") {
                "password" => "<div class='popup warning fit'><span style='color:black;'>Mot de passe incorrect</span></div>",
                "no-change" => "<div class='popup warning fit'><span style='color:black;'>Aucun changement apporté</span></div>",
                "equals" => "<div class='popup warning fit'><span style='color:black;'>Mot de passe identique à l'ancien.</span></div>",
                "success" => "<div class='popup fit'><span style='color:black;'>Profil mis à jour</span></div>",
                "ok" => "<div class='popup fit'><span style='color:black;'>{$this->container->lang['image_saved']}</div>",
                "error" => "<div class='popup warning fit'><span style='color:black;'>{$this->container->lang['image_error']}</span></div>",
                default => ""
            },
        );
        foreach ($phtmlVars as $key => $value) {
            $html = str_replace("%" . $key . "%", $value, $html);
        }
        return $html;
    }

    private function login(): string
    {
        $popup = match (filter_var($this->request->getQueryParam('info'), FILTER_SANITIZE_STRING) ?? "") {
            "nouser" => "<div class='popup warning fit'><span style='color:black;'>Utilisateur introuvable</span></div>",
            "password" => "<div class='popup warning fit'><span style='color:black;'>Mot de passe incorrect</span></div>",
            "not_logged" => "<div class='popup warning fit'><span style='color:black;'>Non connecté</span></div>",
            "pc" => "<div class='popup fit'><span style='color:black;'>Mot de passe changé</span></div>",
            "deleted" => "<div class='popup warning fit'><span style='color:black;'>Compte supprimé</span></div>",
            default => ""
        };
        $html = <<<HTML
            <div class="container">
                <h1 class="text-center text-white">Connexion</h1>
                $popup
                <form method="post" action="{$this->container['router']->pathFor('accounts', ['action' => 'login'])}">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required/>
                    <button type="submit" class="btn btn-sm btn-primary" value="OK" name="sendBtn">Connexion</button>
                    <a href="{$this->container['router']->pathFor('accounts', ['action' => 'register'])}" class="btn btn-sm btn-default">Inscription</a>
                </form>
            </div>
        </body>
        </html>
        HTML;
        return genererHeader("Connexion", []) . $html;
    }

    private function register(): string
    {
        $popup = match (filter_var($this->request->getQueryParam('info'), FILTER_SANITIZE_STRING) ?? "") {
            "invalid" => "<div class='popup warning'>Formulaire invalide</div>",
            "password" => "<div class='popup warning'>Mot de passe invalide</div>",
            default => ""
        };
        $html = <<<HTML
            <div class="container">
                <h1 class="text-center text-white">Inscription</h1>
                $popup
                <form method="post" action="{$this->container['router']->pathFor('accounts', ['action' => 'register'])}">
                    <label class="form-control-label" for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" class="form-control form-control-alternative" required autofocus>
                    <label class="form-control-label" for="lastname">Nom de famille</label>
                    <input type="text" id="lastname" name="lastname" class="form-control form-control-alternative" required>
                    <label class="form-control-label" for="firstname">Prénom</label>
                    <input type="text" id="phone" name="phone" class="form-control form-control-alternative" required>
                    <label class="form-control-label" for="phone">Téléphone</label>
                    <input type="text" id="firstname" name="firstname" class="form-control form-control-alternative" required>
                    <label class="form-control-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control form-control-alternative" required>
                    <label class="form-control-label" for="input-new-password">Mot de passe</label>
                    <input type="password" minlength="14" maxlength="40" pattern="(?=.*\d)(?=.*[a-z])(?=.*[~!@#$%^&*()\-_=+[\]{};:,<>\/?|])(?=.*[A-Z]).{14,}" id="input-new-password" name="password" class="form-control form-control-alternative" required/>
                    <label class="form-control-label" for="input-new-password-c">Confirmation du mot de passe</label>
                    <input type="password" minlength="14" maxlength="40" pattern="(?=.*\d)(?=.*[a-z])(?=.*[~!@#$%^&*()\-_=+[\]{};:,<>\/?|])(?=.*[A-Z]).{14,}" id="input-new-password-c" name="password-confirm" class="form-control form-control-alternative" required/>
                    <button type="submit" class="btn btn-sm btn-primary" value="OK" name="sendBtn">Inscription</button>
                    <a href="{$this->container['router']->pathFor('accounts', ['action' => 'login'])}">Connexion</a>
                </form>
            </div>
        </body>
        </html>
        HTML;
        return genererHeader("Inscription", ["profile.css"]) . $html;
    }

    public function render(int $method, int $access_level = Renderer::OTHER_MODE): string
    {
        return match ($method) {
            Renderer::HOME_HOME => $this->showHome(),
            Renderer::LOGIN => $this->login(),
            Renderer::SHOW => $this->show(),
            Renderer::REGISTER => $this->register(),
            default => parent::render($method, $access_level),
        };
    }
}