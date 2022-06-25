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

    public function __construct(private TemplateEngine $templateEngine)
    {

    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = $request->getParsedBody();

        if (is_array($body)) {
            // === Extend handling ===
            if (str_contains($body["webpage"], 'extend(')) {
                $parent = $this->templateEngine->processExtend($body["webpage"]);

                // === Block handling ===
                $body["webpage"] = $this->templateEngine->processBlocks($body["webpage"], $parent);
            } else {
                new Response('1.1', 200, textBody: "<h1>Extend keyword does not exist. Blocks will not function</h1><br>");
            }

            // === Argument handling ==
            if (array_key_exists("args", $body)) {
                $body["webpage"] = $this->templateEngine->processArguments($body["webpage"], $body["args"]);
                $body["webpage"] = $this->templateEngine->processForloops($body["webpage"], $body["args"]);
                $body["webpage"] = $this->templateEngine->processCompare($body["webpage"], $body["args"]);

                $request = $request->withParsedBody(["body" => $body["webpage"]]);
                return new Response('1.1', 200, textBody: $request->getParsedBody()["body"]);
            } else {
                return new Response('1.1', 200, textBody: $body["webpage"]);
            }

        }

        return new Response('1.1', 200, textBody: "");
    }
}