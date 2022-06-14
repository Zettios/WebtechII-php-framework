<?php

namespace Webtek\Core;

use App\Entity\Users\User;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Webtek\Core\Database\DBConnection;
use Webtek\Core\DependencyInjection\Container;
use Webtek\Core\Http\Response;
use Webtek\Core\Http\ServerRequest;
use Webtek\Core\Middleware\FallbackRequestHandler;
use Webtek\Core\Middleware\NotFoundMiddleware;
use Webtek\Core\Middleware\StackRequestHandler;
use Webtek\Core\Routing\Route;
use Webtek\Core\Routing\Router;
use Webtek\Core\Templating\TemplateEngine;

class Kernel
{
    private array $middlewares;
    private array $entities;
    private Container $container;

    public function __construct()
    {
        // Initializing HTTP server request message from globals
        $request = ServerRequest::createFromGlobals();

        // Retrieving middleware
        $this->middlewares = require(dirname(__DIR__) . '/config/middleware.php');

        // Retrieve entities
        $this->entities = require(dirname(__DIR__) . '/config/entities.php');

        // Setting up container
        $container = $this->container = new Container();
        $this->setupContainer();

        // Delegating request handling and retrieving to request handler
        $mainHandler = $container->get(StackRequestHandler::class);
        foreach ($this->middlewares as $middleware) {
            $mainHandler->add($container->get($middleware));
        }
        $response = $mainHandler->handle($container->get(ServerRequest::class));
        // Writing response
        $this->writeToOutput($response);
    }

    public function setupContainer(): void
    {
        $di = $this->container;

        // Registering main request
        $di->register(ServerRequest::class, function () {
            return ServerRequest::createFromGlobals();
        }, instantCreate: true);

        // Registering main request handlers and default fallback middleware
        $di->register(StackRequestHandler::class);
        $di->register(FallbackRequestHandler::class);
        $di->register(NotFoundMiddleware::class);

        // Registering router & related router classes (such as middleware)
        $di->register(Router::class);
        $di->register(Route::class);
        $di->register(TemplateEngine::class);

        $di->register(DBConnection::class);

        // Register main middleware
        foreach ($this->middlewares as $middleware) {
            $di->register($middleware);
        }

        // Register entities
        foreach ($this->entities as $entity) {
            $di->register($entity);
        }


        // Registering all controllers
        $di->registerControllers();

        // Registering logger
        $di->register(LoggerInterface::class, Logger::class, ["name" => "webtek"]);
    }

    private function writeToOutput(Response $res) {
        foreach (array_keys($res->getHeaders()) as $header) {
            header($res->getHeaderLine($header));
        }

        http_response_code($res->getStatusCode());

        echo $res->getTextBody();
    }
}