<?php

namespace Webtek\Core\Routing;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use Webtek\Core\Http\Request;

class Router
{

    private array $routes = [];
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getRoute(array $controllers)
    {
        foreach ($controllers as $controller){
            $refl = new ReflectionClass($controller);
            foreach ($refl->getMethods() as $method){
                $attributes = $method->getAttributes(Route::class);
                foreach ($attributes as $attribute){
                    $route = $attribute->newInstance();
                    $this->register($route->getMethod(), $route->getPath(), [$refl->getName(), $method->getName()]);
                }
            }
        }
    }

    public function register(string $requestMethod, string $route, callable|array $callable): self
    {
        $this->routes[$requestMethod][$route] = $callable;
        return $this;
    }

    public function createArguments(string $queryString): ?array
    {
        $arr = preg_split('/[&|=]/', $queryString);
        $args = [];
        for ($i = 0; $i < sizeof($arr); $i+=2) {
            if ($i+1 > sizeof($arr)-1){
                $args[$arr[$i]] = "";
            } else {
                $args[$arr[$i]] = $arr[$i+1];
            }
            if ($i+2 > sizeof($arr)-1) {
                break;
            }
        }
        return $args;
    }

    public function getView(Request $request): ?array
    {
        $uri = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();
        $args = $this->createArguments($query);
        $requestMethod = $request->getMethod();

        if (array_key_exists($uri, $this->routes[$requestMethod])){
            // If the url doesn't end with a "/"
            return $this->callControllerFunction($requestMethod, $uri, $args);
        } else {
            // If the url ends with a "/" check for it.
            if (str_ends_with($uri, "/")) {
                $uri = substr($uri, 0, -1);
                if (array_key_exists($uri, $this->routes[$requestMethod])) {
                    return $this->callControllerFunction($requestMethod, $uri, $args);
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }
    }

    public function callControllerFunction($requestMethod, $uri, $args): array
    {
        $body = call_user_func($this->routes[$requestMethod][$uri], $args);

        if (count($body[1]) !== 0) {
            foreach ($body[1] as $key => $value) {
                $args[$key] = $value;
            }
        }
        return array_merge(["webpage" => $body[0]], ["args" => $args]);
    }

    public function resolve(Request $request)
    {
        $controllers = $this->container->registeredControllers;
        $this->getRoute($controllers);
        return $this->getView($request);
    }
}