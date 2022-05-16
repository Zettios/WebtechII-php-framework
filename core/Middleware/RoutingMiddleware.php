<?php

namespace Webtek\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Webtek\Core\Http\Response;
use Webtek\Core\Routing\Router;
use Webtek\Core\Templating\TemplateEngine;

class RoutingMiddleware implements MiddlewareInterface
{

    public function __construct(private Router $router) {}

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Check if url route exists in router
        $responseText = $this->router->resolve($request);

        if (!is_null($responseText)) {
            $request = $request->withParsedBody($responseText);
            return $handler->handle($request);
        } else {
            return new Response('1.1', 404, textBody: "Error");
        }
    }
}