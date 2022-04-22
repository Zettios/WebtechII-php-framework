<?php

namespace Webtek\Core\Routing;


//TODO: Error handling
use ReflectionClass;
use Webtek\Core\Http\Request;

class Router
{

    private array $routes = [];

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

    public function getView(Request $request): string
    {
        $uri = $request->getUri()->getPath();
        $requestMethod = $request->getMethod();
        if (array_key_exists($uri, $this->routes[$requestMethod])){
            return call_user_func($this->routes[$requestMethod][$uri]);
        } else {
            if (str_ends_with($uri, "/")) {
                $uri = substr($uri, 0, -1);
                if (array_key_exists($uri, $this->routes[$requestMethod])) {
                    return call_user_func($this->routes[$requestMethod][$uri]);
                } else {
                    return "";
                }
            } else {
                return "";
            }
        }
    }

    public function resolve(Request $request, array $controllers) {
        $this->getRoute($controllers);
        $response = $this->getView($request);
        echo $response;
    }
}