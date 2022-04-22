<?php

namespace Webtek\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Webtek\Core\Http\Response;

class FallbackRequestHandler implements RequestHandlerInterface
{
    
    public function __construct(private NotFoundMiddleware $notFoundMiddleware) {}

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->notFoundMiddleware->process($request);
    }
}