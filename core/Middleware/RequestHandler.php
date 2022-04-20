<?php

namespace Webtek\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Webtek\Core\Http\Response;

class RequestHandler implements RequestHandlerInterface
{
    private array $stack = [];

    public function add(MiddlewareInterface $requestHandler) {
        $stack[] = $requestHandler;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // If nothing is on the stack anymore, report 404 not found
        if (count($this->stack) === 0) {
            return new Response("1.1", 404);
        }

        // Select the next first middleware from the stack
        $handler = array_shift($this->stack);
        // Let it be processed by the middleware
        return $handler->process($request);
    }
}