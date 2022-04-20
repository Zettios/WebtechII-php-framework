<?php

namespace Webtek\Core\Routing;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class Router implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger) {}

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        if (!$routes = file_get_contents(dirname(__DIR__, 2) . "/config/RouteConfig.json")) {
            $this->logger->error("RouteConfig.json not found in folder config.");
        }

        $routes = json_decode($routes);

        foreach($routes->routes as $item) {
            if ($item->path === $path) {
                $sources = array_slice(scandir($item->source),2);
                foreach ($sources as $controllers) {
                    if (str_ends_with($controllers, ".php")) {
                        $this->logger->info("Controller found: " . substr($controllers, 0, -4));
                    }
                }
            }
        }

        return $handler->handle($request);
    }
}