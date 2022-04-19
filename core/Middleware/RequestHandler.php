<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Webtek\Core\RequestHandling\Response;

class RequestHandler implements RequestHandlerInterface
{
    private array $stack = [];

    public function add(RequestHandlerInterface $requestHandler) {
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
        if (count($this->stack) === 0) {
            return new Response('1.1', 404, 'Page not found.', [], []);
        }

        $handler = array_shift($this->stack);
        return $handler->process($request);
    }
}