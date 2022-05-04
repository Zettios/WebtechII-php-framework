<?php

namespace Webtek\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Webtek\Core\Http\Response;
use Webtek\Core\Routing\Router;

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
            $response = new Response('1.1', 200, textBody: $responseText);
            $body = $response->getTextBody();

            $needleArg = "{{";
            $needleBlock = "{%";
            $lastPos = 0;
            $positionsArg = array();
            $positionsBlock = array();
            $args = array();
            $blocks = array();

            // === Arguments ==

            while (($lastPos = strpos($body, $needleArg, $lastPos)) !== false) {
                $positionsArg[] = $lastPos;
                $lastPos = $lastPos + strlen($needleArg);
            }

            foreach ($positionsArg as $currentPos) {
                $pos = $currentPos;
                $arg = "";
                while ($body[$pos].$body[$pos+1] !== "}}") {
                    $arg .= $body[$pos];
                    $pos++;
                }
                $args[] = $arg."}}";
            }

            foreach ($args as $arg){
                $body = str_replace($arg, "Pog", $body);
            }
            $response = $response->withTextBody($body);

            // === Blocks ==
            while (($lastPos = strpos($body, $needleBlock, $lastPos)) !== false) {
                $positionsBlock[] = $lastPos;
                $lastPos = $lastPos + strlen($needleBlock);
            }

            foreach ($positionsBlock as $currentPos) {
                $pos = $currentPos;
                $block = "";
                while ($body[$pos].$body[$pos+1] !== "%}") {
                    $block .= $body[$pos];
                    $pos++;

                }
                $blocks[] = $block."%}";
            }

            foreach ($blocks as $block){
                $body = str_replace($block, "Pogie", $body);
            }
            $response = $response->withTextBody($body);

            return $response;
        } else {
            return $handler->handle($request);
        }
    }
}