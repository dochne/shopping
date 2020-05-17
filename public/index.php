<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Dochne\Shopping\Router;
use DI\Bridge\Slim\Bridge;
use DI\Container;
use Twig\Environment;

chdir(__DIR__ . "/..");

require 'vendor/autoload.php';

$container = new Container();
//new \DI\ContainerBuilder()
$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->useAnnotations(true);

$container = $containerBuilder->build();

$loader = new \Twig\Loader\FilesystemLoader();
$loader->setPaths(__DIR__ . "/../src/Templates");
$container->set("twig", new Environment($loader));

// Instantiate App
//$app = AppFactory::create();
$app = Bridge::create($container);

// Add error middleware
$app->addErrorMiddleware(true, true, true);
$app->addMiddleware(new \Dochne\Shopping\Middleware\JsonBodyParserMiddleware());

(new Router())->apply($app);

// Add routes
//$app->get('/', function (Request $request, Response $response) {
//    $response->getBody()->write('<a href="/hello/world">Try /hello/world</a>');
//    return $response;
//});
//
//
//$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
//    $name = $args['name'];
//    $response->getBody()->write("Hello, $name");
//    return $response;
//});

$app->run();