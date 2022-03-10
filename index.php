<?php
session_start();

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Slim\{App, Container};
use custombox\db\Eloquent;
use custombox\exceptions\ExceptionHandler;
use custombox\mvc\controllers\ControllerUser;

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
//Utilisateurs

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
    return <<<HTML
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset='UTF-8'>
        <link rel="icon" href="/www/arnoux23u/crazycharlyday/assets/images/logos/favico.png">
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
        <title>$title</title>
    HTML;
}