<?php

namespace Webtek\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Webtek\Core\Auth\Auth;
use Webtek\Core\Http\Response;

class AuthorizationMiddleware implements MiddlewareInterface
{
    public function __construct(private Auth $auth) {}

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $cookies = $request->getCookieParams();
        $attr = $request->getAttributes();

        $accessLevel =  intval($attr['access']);
        $userAccess =  intval($cookies['accessRole']);

//        echo "<pre>";
//        var_dump($accessLevel)."<br>";
//        var_dump($userAccess)."<br>";
//        echo "</pre>";

        if ($accessLevel === -1) {
            return $handler->handle($request);
        } else {
            if ($accessLevel <= $userAccess) {
                return $handler->handle($request);
            } else {
                return new Response('1.1', 403, textBody: "Not authorized.");
            }
        }
    }
}