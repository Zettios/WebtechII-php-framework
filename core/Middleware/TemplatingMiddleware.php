<?php

namespace Webtek\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Webtek\Core\Http\Response;
use Webtek\Core\Templating\TemplateEngine;

class TemplatingMiddleware implements MiddlewareInterface
{

    public function __construct(private TemplateEngine $templateEngine) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = $request->getParsedBody();

        if (is_array($body)) {
            // === Arguments ==
            echo $this->templateEngine->processArguments($body["webpage"], $body["args"]);
        }


//
//        // === Blocks ==
//        $this->templateEngine->processBlocks($request);

        return new Response('1.1', 200, textBody: "Success");;
    }
}