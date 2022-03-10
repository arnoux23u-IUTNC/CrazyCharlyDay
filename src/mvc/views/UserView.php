<?php

namespace custombox\mvc\views;

use custombox\exceptions\ForbiddenException;
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
            <div class="contenu">
                <div class="notionContenu">
                    <h2 class="title">Les Notions clés</h2>
                    <img alt="notions" src="/assets/images/imgs/notionsdiv.png" class="notionDivIMG">
                </div>
                <div class="parag1">
                    <div>
                        <h1 class="title">L'ATELIER 17.91 C'EST QUOI ?</h1>
                    </div>
                    <div>
                        <h2 class="beneTitle">L'association contribue à des solutions créatives et solidaires, en toute
                        confiance.</h2>
                    </div>
                    <div>
                        <a class="createBox" href="{$this->container['router']->pathFor('creerCommande')}">Créer ma commande</a>
                    </div>
                    <div>
                        <p> L’idée est venue du constat que l’isolement et la précarité sont des situations qui peuvent toucher
                        tout le monde,
                        à toutes étapes de la vie. Etre issue d’une “bonne” classe sociale ne garantit pas que l’on sera
                        accompagné par un cercle d’amis ou familial toute la vie.
                        Des situations peuvent avoir lieu peu importe son âge, son origine, son emploi, sa situation
                        professionnelle et financière.
                        Avec l’Atelier 17.91, nous souhaitons contribuer à améliorer la société de demain en créant du lien
                        entre les personnes,
                        montrer que des dispositifs existent quelle que soit sa situation personnelle,
                        et surtout que l’isolement, l’exclusion et la précarité ne doivent pas définir la nature d’une
                        personne.</p>
                    </div>
                </div>
                <div class="parag2">
                    <div>
                        <h2 class="title">Pourquoi avez-vous créer l'Atelier 17.91 ?</h2>
                    </div>
                    <div>
                        <p> La crise sanitaire nous a fait prendre conscience de la nécessité de "retourner sur le terrain", d'être
                        au
                        contact des personnes et créer du lien avec et entre elles.
                        Après une expérience significative dans l'associatif, nous avions envie de créer quelque chose à notre
                        image, respectant des valeurs qui nous sont chères,
                        et qui répondent à des problématiques sociales et sociétales actuelles. C'est tout naturellement que
                        l'Atelier 17.91 a vu le jour !</p>
                    </div>
                </div>
                <div class="parag3">
                    <div>
                        <h2 class="title">Nos missions</h2>
                    </div>
                    <div>
                        <img src="/assets/images/imgs/ourmissions.png" class="missionPicture">
                    </div>
                </div>
            </div>
            <footer>
                <div class="footer">
                    <p>©2022 L'ATELIER 17.91</p>
                </div>
            </footer>
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
            "password" => "<div class='popup warning'>Mot de passe ne correspondent pas</div>",
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
                    <label class="form-control-label" for="phone">Prénom</label>
                    <input type="text" id="firstname" name="firstname" class="form-control form-control-alternative" required>
                    <label class="form-control-label" for="firstname">Téléphone</label>
                    <input type="text" id="phone" name="phone" class="form-control form-control-alternative" required>
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

    /**
     * @throws ForbiddenException
     */
    private function list(): string
    {
        $usersHtml = "";
        foreach (User::where('user_id', '!=', $this->user['user_id'])->get() as $user) {
            $usersHtml .= (new UserView($this->container, $user, $this->request))->render(Renderer::SHOW_IN_LIST, user: $user);
        }
        return genererHeader("Liste des utilisateurs") . $usersHtml;
    }

    #[Pure] private function forList($user): string
    {
        $adminBtn = $user->isAdmin() ? "<a href='{$this->container['router']->pathFor('switchAdmin', ['id' => $user['user_id']])}' class='btn btn-sm btn-primary'>Supprimer droit Admin</a>" : "<a href='{$this->container['router']->pathFor('switchAdmin', ['id' => $user['user_id']])}' class='btn btn-sm btn-danger'>Ajouter droit Admin</a>";
        return <<<HTML
            <div style="border: 1px solid black;" class="container">
                <p>Utilisateur : {$user['username']}</p>
                <p>Nom : {$user['lastname']}</p>
                <p>Prénom : {$user['firstname']}</p>
                <p>Email : {$user['mail']}</p>
                <p>Téléphone : {$user['phone']}</p>
                <p>Date de création : {$user['created_at']}</p>
                <p>Date de modification : {$user['updated']} - {$user['last_ip']}</p>
                $adminBtn
            </div>
        HTML;
    }

    public function render(int $method, int $access_level = Renderer::OTHER_MODE, $user = null): string
    {
        return match ($method) {
            Renderer::HOME_HOME => $this->showHome(),
            Renderer::LOGIN => $this->login(),
            Renderer::SHOW_IN_LIST => $this->forList($user),
            Renderer::SHOW_ALL => $this->list(),
            Renderer::SHOW => $this->show(),
            Renderer::REGISTER => $this->register(),
            default => parent::render($method, $access_level),
        };
    }
}