<?php

namespace Webtek\Core\Routing;


//TODO: Error handling
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
            $body = call_user_func($this->routes[$requestMethod][$uri], $args);
            return array_merge(["webpage" => $body], ["args" => $args]);
        } else {
            if (str_ends_with($uri, "/")) {
                $uri = substr($uri, 0, -1);
                if (array_key_exists($uri, $this->routes[$requestMethod])) {
                    $body = call_user_func($this->routes[$requestMethod][$uri], $args);
                    return array_merge(["webpage" => $body], ["args" => $args]);
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }
    }

    public function resolve(Request $request) {
        $controllers = $this->container->registeredControllers;
        $this->getRoute($controllers);
        return $this->getView($request);
    }
}