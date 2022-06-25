<?php

namespace Webtek\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Webtek\Core\Http\Response;

class StackRequestHandler implements RequestHandlerInterface
{
    private array $stack = [];
    private RequestHandlerInterface $fallbackHandler;

    public function __construct(FallbackRequestHandler $fallbackHandler)
    {
        $this->fallbackHandler = $fallbackHandler;
    }

    public function add(MiddlewareInterface $requestHandler)
    {
        $this->stack[] = $requestHandler;
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
            return $this->fallbackHandler->handle($request);
        }

        // Select the next first middleware from the stack
        $middleware = array_shift($this->stack);

        // Let it be processed by the middleware
        return $middleware->process($request, $this);
    }
}