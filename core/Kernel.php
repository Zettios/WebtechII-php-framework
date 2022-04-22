<?php

namespace Webtek\Core;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Webtek\Core\DependencyInjection\Container;
use Webtek\Core\Http\Response;
use Webtek\Core\Http\ServerRequest;
use Webtek\Core\Middleware\AuthorizationMiddleware;
use Webtek\Core\Middleware\FallbackRequestHandler;
use Webtek\Core\Middleware\NotFoundMiddleware;
use Webtek\Core\Middleware\RoutingMiddleware;
use Webtek\Core\Middleware\StackRequestHandler;
use Webtek\Core\Routing\Route;
use Webtek\Core\Routing\Router;

class Kernel
{
    private Container $container;

    public function __construct()
    {
        // Initializing HTTP server request message from globals
        $request = ServerRequest::createFromGlobals();

        // Setting up container
        $container = $this->container = new Container();
        $this->setupContainer();

        // Delegating request handling and retrieving to request handler
        $mainHandler = $container->get(StackRequestHandler::class);
        $response = $mainHandler->handle($request);

        // Writing response
        $this->writeToOutput($response);
    }

    public function setupContainer(): void
    {
        $di = $this->container;

        // Registering main request handlers and default fallback middleware
        $di->set(StackRequestHandler::class);
        $di->set(FallbackRequestHandler::class);
        $di->set(NotFoundMiddleware::class);

        // Registering router & related router classes (such as middleware)
        $di->set(Router::class);
        $di->set(Route::class);

        // Register main middleware
        $middlewares = require('../middleware.php');
        foreach ($middlewares as $middleware) {
            $di->set($middleware);
        }

        // Registering logger
        $di->set(LoggerInterface::class, Logger::class, ["name" => "webtek"]);
    }

    private function writeToOutput(Response $res) {
        foreach (array_keys($res->getHeaders()) as $header) {
            header($res->getHeaderLine($header));
        }

        http_response_code($res->getStatusCode());

        echo $res->getTextBody();
    }
}