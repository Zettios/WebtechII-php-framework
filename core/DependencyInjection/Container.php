<?php

namespace Webtek\Core\DependencyInjection;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionNamedType;
use Webtek\Core\DependencyInjection\Exception\ContainerException;
use Webtek\Core\DependencyInjection\Exception\NotFoundException;


class Container implements ContainerInterface
{
    private array $registeredClasses = [];
    private array $registeredFactories = [];
    private array $createdClasses = [];

    /**
     * Registers which class to use for certain classfqcn/interface/class name in constructors.
     *
     * @param string $id ID, or parameter type used in constructors or methods.
     * @param null|string|callable $concrete The actual class that the id wants to fetch whenever resolving, or factory callable that will return that class.
     * @param array $staticParameters Configuration of manually defined parameters
     *
     * @return void
     */
    public function set(string $id, null|string|callable $concrete = null, array $staticParameters = []): void
    {
        if ($concrete === null) {
            $concrete = $id;
        } elseif (is_callable($concrete)) {

        }

        $this->registeredClasses[$id] = [
            "class" => $concrete,
            "staticParameters" => $staticParameters
        ];
    }

    public function resolve(ReflectionClass $reflection, array $staticParameters = []): array
    {
        $cons = $reflection->getConstructor();
        $params = [];

        if (isset($cons)) {
            foreach ($cons->getParameters() as $param) {
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

        $reflection = new ReflectionClass($this->registeredClasses[$id]["class"]);
        $params = $this->resolve($reflection, $this->registeredClasses[$id]["staticParameters"]);
        $nClass = $reflection->newInstance(...$params);
        $this->createdClasses[$id] = $nClass;
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
        return array_key_exists($id, $this->registeredClasses);
    }
}