<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Webtek\Core\RequestHandling\Response;

class RequestHandler implements RequestHandlerInterface
{

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // TODO: Implement handle() method.

        return new Response();
    }
}