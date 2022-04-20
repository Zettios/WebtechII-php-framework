<?php

namespace Webtek\Core;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Webtek\Core\DependencyInjection\Container;
use Webtek\Core\Http\Response;
use Webtek\Core\Http\ServerRequest;
use Webtek\Core\Middleware\RequestHandler;
use Webtek\Core\Routing\Router;

class Kernel
{
    public function __construct()
    {
        // Initializing HTTP server request message from globals
        $request = ServerRequest::createFromGlobals();

        // Setting up container
        $di = new Container();
        $di->set(RequestHandler::class);
        $di->set(Router::class);
        $di->set(LoggerInterface::class, Logger::class, ["name" => "webtek"]);
        $di->get(LoggerInterface::class)->pushHandler(new StreamHandler("php://stdout"));

        // Adding middleware to the request handler
        $di->get(RequestHandler::class)->add($di->get(Router::class));

        // Execute request handler

        $res = $di->get(RequestHandler::class)->handle($request);
        $this->writeToOutput($res);
    }

    private function writeToOutput(Response $res) {

        foreach (array_keys($res->getHeaders()) as $header) {
            header($res->getHeaderLine($header));
        }

        http_response_code($res->getStatusCode());

        echo $res->getTextBody();
    }
}