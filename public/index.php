<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;
use App\Middleware\AddJsonResponseHeader;

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';

$builder = new ContainerBuilder();

$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();

$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$error_middleware = $app->addErrorMiddleware(true, true, true);

$error_handler = $error_middleware->getDefaultErrorHandler();

$error_handler->forceContentType('application/json');

$app->add(new AddJsonResponseHeader);

$app->get('/api/products', function (Request $request, Response $response) {

    $repository = $this->get(App\Repositories\ProductRepository::class);

    $data = $repository->getAll();

    $body = json_encode($data);

    $response->getBody()->write($body);

    return $response;
});

$app->get('/api/products/{id:[0-9]+}', function (Request $request, Response $response, string $id) {

    $repository = $this->get(App\Repositories\ProductRepository::class);

    $product = $repository->getById((int) $id);

    if ($product === false) {
        throw new \Slim\Exception\HttpNotFoundException(
            $request,
            message: 'product not found'
        );
    }

    $body = json_encode($product);

    $response->getBody()->write($body);

    return $response;
});

$app->run();
