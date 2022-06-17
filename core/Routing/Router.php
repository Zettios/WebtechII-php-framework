<?php

namespace Webtek\Core\Routing;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use Webtek\Core\Http\Request;
use Webtek\Core\Http\Response;

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
            foreach ($refl->getMethods() as $method) {
                if (!str_contains($method->getFileName(), "AbstractController")){
                    $attributes = $method->getAttributes(Route::class);
                    foreach ($attributes as $attribute){
                        $route = $attribute->newInstance();

                        $this->register($route->getMethod(), $route->getPath(), [$refl, $method->getName()]);
                    }
                }
            }
        }
    }

    public function createParameters(Request $request): array|Response
    {
        $uri = $request->getUri()->getPath();
        $requestMethod = $request->getMethod();
        $methodParameters = [];

        if (array_key_exists($uri, $this->routes[$requestMethod])){
            $entry = $this->routes[$requestMethod][$uri];
            $refl = $entry[0];
            $method = $entry[1];
            $parameters = $refl->getMethod($method)->getParameters();

            foreach ($parameters as $parameter) {
                if ($this->container->has($parameter->getType()->getName())) {
                    $object = $this->container->get($parameter->getType()->getName());
                    $methodParameters[$parameter->getName()] = $object;
                } else {
                    return new Response('1.1', 424, textBody: "Dependency '".$parameter->getName()."' not found");
                }
            }
        } else {
            return new Response('1.1', 404, textBody: "Path '".$uri."' not found");
        }
        return $methodParameters;
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

    public function getView(Request $request, array $methodParameters): array|Response
    {

        $uri = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();
        $args = $this->createArguments($query);
        $requestMethod = $request->getMethod();

        if (array_key_exists($uri, $this->routes[$requestMethod])){
            // If the url doesn't end with a "/"
            return $this->callControllerFunction($requestMethod, $uri, $methodParameters);
        } else {
            // If the url ends with a "/" check for it.
            if (str_ends_with($uri, "/")) {
                $uri = substr($uri, 0, -1);
                if (array_key_exists($uri, $this->routes[$requestMethod])) {
                    return $this->callControllerFunction($requestMethod, $uri, $methodParameters);
                } else {
                    return new Response('1.1', 404, textBody: "Path '".$uri."' not found");
                }
            } else {
                return new Response('1.1', 404, textBody: "Path '".$uri."' not found");;
            }
        }
    }

    public function callControllerFunction($requestMethod, $uri, $parameters): array
    {
        $class = $this->container->get($this->routes[$requestMethod][$uri][0]->getName());
        $method = $this->routes[$requestMethod][$uri][1];
        $body = call_user_func_array(array($class, $method), $parameters);

        if (count($body[1]) !== 0) {
            foreach ($body[1] as $key => $value) {
                $args[$key] = $value;
            }
        }
        if (isset($args)) {
            return array_merge(["webpage" => $body[0]], ["args" => $args]);
        } else {
            return array_merge(["webpage" => $body[0]], []);
        }
    }

    public function resolve(Request $request): array|Response
    {
        $controllers = $this->container->registeredControllers;
        $path = "..".$request->getUri()->getPath();
        if (file_exists($path) && is_file($path)) {
            return new Response('1.1', 302, textBody: file_get_contents($path));
        }

        if (count($controllers) === 0) {
            return new Response('1.1', 404, textBody: "No existing controller.");
        }

        //Get the method routes
        $this->getRoute($controllers);
        if (count($this->routes) === 0) {
            return new Response('1.1', 404, textBody: "Controllers have no methods to process.");
        }

        //Make the parameters of the method
        $methodParameters = $this->createParameters($request);
        if (is_a($methodParameters, Response::class)) {
            return $methodParameters;
        }

        //Execute the method
        return $this->getView($request, $methodParameters);
    }
}