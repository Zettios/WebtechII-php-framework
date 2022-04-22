<?php

namespace Webtek\Core\Routing;



use ReflectionClass;
use Webtek\Controllers\AbstractController;
use Webtek\Core\RequestHandling\Request;

//TODO: Error handling
class Router
{
    public array $routes = [];
    public array $controllers = [];
    public object $config;

    public function getConfig(): object
    {
        if (file_get_contents("../config/RouteConfig.json") === false) {
            echo "RouteConfig.json not found in folder config.";
        }
        //TODO: Error handling

        $routes = file_get_contents("../config/RouteConfig.json");
        return $routes = json_decode($routes);
    }

    public function registerControllers(string $dir = "../controllers")
    {
        $controllers = array_slice(scandir($dir), 2);
        foreach ($controllers as $controller){
            $path = $dir."/".$controller;
            if (is_dir($path)){
                $this->registerControllers($path);
            } else {
                if (str_ends_with($controller, ".php")) {
                    $controller = substr($controller, 0, -4);
                    foreach ($this->config->routes as $route){
                        if ($route->source === $dir){
                            $controllerPath = $route->psr4.$controller;
                            $refl = new ReflectionClass($controllerPath);

                            if (!$refl->isAbstract()){
                                $this->controllers[$controller] = $refl;
                            }
                        }
                    }
                }
            }
        }
    }

    public function getRoute(array $reflcControllers)
    {
        foreach ($reflcControllers as $reflcController){
            foreach ($reflcController->getMethods() as $method){
                $attributes = $method->getAttributes(Route::class);
                foreach ($attributes as $attribute){
                    $route = $attribute->newInstance();

                    $this->register($route->getMethod(), $route->getPath(), [$reflcController->getName(), $method->getName()]);
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

    //TODO: Error handling
    public function resolve(Request $request) {
        $this->config = $this->getConfig();
        $this->registerControllers();
        $this->getRoute($this->controllers);
        $response = $this->getView($request);
        echo $response;
    }
}