<?php
session_start();

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Slim\{App, Container};
use custombox\db\Eloquent;
use custombox\exceptions\ExceptionHandler;
use custombox\mvc\controllers\{ControllerBoite, ControllerCommande, ControllerUser, ControllerProduits};

#Container
$container = new Container();
$container['settings']['displayErrorDetails'] = true;
$container['notFoundHandler'] = function () {
    return function ($request, $response) {
        $html = file_get_contents('errors' . DIRECTORY_SEPARATOR . '404.html');
        return $response->withStatus(404)->write($html);
    };
};
$container['notAllowedHandler'] = function () {
    return function ($request, $response) {
        $html = file_get_contents('errors' . DIRECTORY_SEPARATOR . '405.html');
        return $response->withStatus(405)->write($html);
    };
};
$container['errorHandler'] = function () use ($container) {
    return new ExceptionHandler($container);
};
$container['categories_img_dir'] = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'categories';
$container['produits_img_dir'] = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'produits';

#Connexion à la base de données
Eloquent::start('src' . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'conf.ini');
$app = new App($container);

#Redirection du traffic dans l'application
//--Comptes
$app->any("/accounts/admin/{id:[0-9]+}", function ($request, $response, $args) {
    return (new ControllerUser($this, $request, $response, $args))->switchAdmin();
})->setName('switchAdmin');
$app->any("/accounts/{action:login|profile|logout|register|delete}[/]", function ($request, $response, $args) {
    return (new ControllerUser($this, $request, $response, $args))->process();
})->setName('accounts');
$app->get("/users[/]", function ($request, $response, $args) {
    return (new ControllerUser($this, $request, $response, $args))->list();
})->setName('usersList');

//--Commandes
$app->any("/commandes/creer[/]", function ($request, $response, $args) {
    return (new ControllerCommande($this, $request, $response, $args))->create();
})->setName('creerCommande');
$app->get("/commandes/{id:[0-9]+}[/]", function ($request, $response, $args) {
    return (new ControllerCommande($this, $request, $response, $args))->show();
})->setName('commande');
$app->any("/commandes[/]", function ($request, $response, $args) {
    return (new ControllerCommande($this, $request, $response, $args))->list();
})->setName('commandesList');


//--Produits
$app->any('/produits/{id:[0-9]+}/edit[/]', function ($request, $response, $args) {
    return (new ControllerProduits($this, $request, $response, $args))->edit();
})->setName('modifierProduit');
$app->any('/produits/creer[/]', function ($request, $response) {
    return (new ControllerProduits($this, $request, $response, []))->create();
})->setName('creerProduit');
$app->get('/produits/{id:[0-9]+}[/]', function ($request, $response, $args) {
    return (new ControllerProduits($this, $request, $response, $args))->display();
})->setName('afficherProduit');
$app->get('/produits[/]', function ($request, $response, $args) {
    return (new ControllerProduits($this, $request, $response, $args))->displayAll();
})->setName('afficherProduits');

//--
$app->any('/boites/{id:[0-9]+}/edit[/]', function ($request, $response, $args) {
    return (new ControllerBoite($this, $request, $response, $args))->edit();
})->setName('modifierBoite');
$app->any('/boites/creer[/]', function ($request, $response) {
    return (new ControllerBoite($this, $request, $response, []))->create();
})->setName('creerBoite');
$app->get('/boites/{id:[0-9]+}[/]', function ($request, $response, $args) {
    return (new ControllerBoite($this, $request, $response, $args))->display();
})->setName('afficherBoite');
$app->get('/boites[/]', function ($request, $response, $args) {
    return (new ControllerBoite($this, $request, $response, $args))->displayAll();
})->setName('afficherBoites');

//--Home
$app->get('/', function ($request, $response) {
    return (new ControllerUser($this, $request, $response, []))->home();
})->setName('home');


#Demmarage de l'application
try {
    $app->run();
} catch (Throwable $e) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error', true, 500);
    echo '<h1>Something went wrong!</h1>';
    print_r($e);
    exit;
}

/**
 * Method who generates the header of the page
 * @param string $title title of the page
 * @param array $styles stylesheets to include
 * @return string html code
 */
function genererHeader(string $title, array $styles = []): string
{
    $html = <<<HTML
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset='UTF-8'>
        <link rel="icon" href="https://webetu.iutnc.univ-lorraine.fr/www/arnoux23u/crazycharlyday/assets/images/logos/favico.png">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="CrazyCharlyDay || CustomBox - Atelier 17.91">
        <meta property="og:title" content="CustomBox - Atelier 17.91" />
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="https://webetu.iutnc.univ-lorraine.fr/www/arnoux23u/crazycharlyday/" />
        <meta property="og:image" content="https://webetu.iutnc.univ-lorraine.fr/www/arnoux23u/crazycharlyday/assets/img/logo.png" />
        <meta property="og:description" content="CrazyCharlyDay || CustomBox - Atelier 17.91" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
        <link href="https://fonts.googleapis.com/css?family=Poiret+One" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <title>$title - CustomBox</title>
        <link rel="stylesheet" href="/assets/css/global.css">
    HTML;
    foreach ($styles as $style)
        $html .= "\n\t<link rel='stylesheet' href='/assets/css/$style'>";
    $html .= "\n</head>\n<body>\n" . file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'navbar.phtml');
    $phtmlVars = array(
        "home" => "https://webetu.iutnc.univ-lorraine.fr/www/arnoux23u/"
    );
    foreach ($phtmlVars as $key => $value) {
        $html = str_replace("%" . $key . "%", $value, $html);
    }
    return $html;
}