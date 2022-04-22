<?php

namespace Webtek\Core\DependencyInjection;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Webtek\Core\DependencyInjection\Exception\ContainerException;
use Webtek\Core\DependencyInjection\Exception\NotFoundException;


class Container implements ContainerInterface
{
    private array $register = [];
    private array $createdClasses = [];
    public array $registeredControllers = [];

    public function __construct() {
        $this->register(ContainerInterface::class, self::class);
        $this->createdClasses[ContainerInterface::class] = $this;
    }

    /**
     * Registers which class to use for certain classfqcn/interface/class name in constructors.
     *
     * @param string $id ID, or parameter type used in constructors or methods.
     * @param null|string|array|callable $concrete The actual class that the id wants to fetch whenever resolving, or factory callable that will return that class.
     * @param array $staticParameters Configuration of manually defined parameters
     *
     * @return void
     */
    public function register(string $id, null|string|array|callable $concrete = null, array $staticParameters = [], bool $singleton = true): void
    {
        if ($concrete === null) {
            $concrete = $id;
        }

        $this->register[$id] = [
            "staticParameters" => $staticParameters,
            "singleton" => $singleton
        ];

        if (is_callable($concrete)) {
            $this->register[$id]["factoryCallable"] = $concrete;
        } elseif (gettype($concrete) == "string") {
            if (class_implements($concrete) && in_array(FactoryInterface::class, class_implements($concrete))) {
                $this->register[$id]["factory"] = $concrete;
            } else {
                $this->register[$id]["class"] = $concrete;
            }
        } else throw new ContainerException("Could not register " . $id . " due to unknown type: " . gettype($concrete) . ' value: ' . $concrete);
    }

    public function registerControllers(string $dir = "../src/Controller"): int
    {
        $routes = $this->getConfig();

        if ($routes === null) {
            return 0;
        }

        $controllers = array_slice(scandir($dir), 2);
        foreach ($controllers as $controller){
            $path = $dir."/".$controller;
            if (is_dir($path)){
                $this->registerControllers($path);
            } else {
                if (str_ends_with($controller, ".php")) {
                    $controller = substr($controller, 0, -4);
                    foreach ($routes->routes as $route) {
                        if ($route->source === $dir) {
                            $controllerPath = $route->psr4 . "\\".$controller;
                            $refl = new ReflectionClass($controllerPath);
                            if (!$refl->isAbstract()) {
                                $createdController = $refl->newInstance();
                                $this->registeredControllers[$controller] = $createdController;
                            }
                        }
                    }
                }
            }
        }
        return 1;
    }

    public function getConfig(): ?object
    {
        if (file_get_contents("../config/RouteConfig.json") === false) {
            echo "RouteConfig.json not found in folder config.";
            return null;
        }

        $routes = file_get_contents("../config/RouteConfig.json");
        return json_decode($routes);
    }

    public function resolve(ReflectionClass|ReflectionMethod $reflection, array $staticParameters = []): array
    {
        if ($reflection instanceof ReflectionClass) {
            $function = $reflection->getConstructor();
        } else {
            $function = $reflection;
        }
        $params = [];

        if (isset($function)) {
            foreach ($function->getParameters() as $param) {
                $paramName = $param->getName();
                $paramType = $param->getType();

                // Check if parameter is builtin/primitive (not a class, trait or interface)
                if ($paramType == null || $paramType->isBuiltin()) {
                    // If it is primitive, search for configuration in register
                    $makingClass = $reflection->getName();
                    if (array_key_exists($paramName, $staticParameters)) {
                        $params[$paramName] = $staticParameters[$paramName];
                    } elseif (!$param->isOptional()) {
                        throw new ContainerException("Unable to resolve " . $makingClass . " because no primitive parameter configuration was given for " . $paramName);
                    }
                } else if ($paramType instanceof ReflectionNamedType) {
                    // If it is not primitive, it must be a class dependency
                    // Check if class is registered before continuing to resolve
                    $paramTypeName = $paramType->getName();

                    if ($this->has($paramTypeName)) {
                        // If the class is registered, check if the class is already created
                        if (isset($this->createdClasses[$paramTypeName])) {
                            // The class seems to have been created before, retrieve from createdClasses
                            $params[$paramName] = $this->createdClasses[$paramTypeName];
                        } else {
                            // The class has NOT been created before, create a new one by resolving
                            $params[$paramName] = $this->get($paramTypeName);
                        }
                    } elseif (!$param->isOptional()) {
                        throw new ContainerException("Registered class dependent on unregistered class");
                    }
                } else {
                    throw new ContainerException("I don't know what the fuck you did");
                }
            }
        }

        return $params;
    }


    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get(string $id): object
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Service is not registered.");
        }

        if (isset($this->createdClasses[$id])) return $this->createdClasses[$id];

        if (isset($this->register[$id]["factoryCallable"])) {
            $reflection = new ReflectionMethod($this->register[$id]["factoryCallable"]);
            $params = $this->resolve($reflection);
            $nClass = $reflection->getClosure()(...$params);
        } elseif (isset($this->register[$id]["factory"])) {
            $reflection = new ReflectionClass($this->register[$id]["factory"]);
            $params = $this->resolve($reflection, $this->register[$id]["staticParameters"]);
            $factory = $reflection->newInstance(...$params);
            $nClass = $factory->newInstance();
        } elseif (isset($this->register[$id]["class"])) {
            $reflection = new ReflectionClass($this->register[$id]["class"]);
            $params = $this->resolve($reflection, $this->register[$id]["staticParameters"]);
            $nClass = $reflection->newInstance(...$params);
        } else {
            throw new ContainerException("Container could not figure out whether or not registered entry concrete contained was class, factory or callable.");
        }

        if ($this->register[$id]["singleton"]) $this->createdClasses[$id] = $nClass;
        return $nClass;

    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->register);
    }
}