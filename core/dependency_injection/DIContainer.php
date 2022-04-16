<?php

namespace Webtek\Core\DependencyInjection;

use http\Exception\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionNamedType;
use Webtek\Core\DependencyInjection\Exception\ContainerException;
use Webtek\Core\DependencyInjection\Exception\NotFoundException;

/**
 * Het doel van een DI container is door de inversion of control toe te passen. Wat houdt dit in?
 * Normaal maak je een klasse aan en daarin nog een klasse. En misschien daarin nog een klasse.
 * Met inversion of control gaat dit anders om. De DI container maakt eerst de klasse aan die een andere klass nodig heeft
 * met reflection.
 * Voorbeeld: Klasse A maakt klasse B aan. En klasse B maakt klasse C en D aan. Dit is hoe het bijvoorbeeld bij java gaat.
 * Voorbeeld inversion of control: Met reflection word er eerst gekeken welke klasse parameter klass A nodig heeft.
 *                                 Daarna word gekeken wat klasse B nodig heeft. Klasse B heeft klasse C en D nodig.
 *                                 Klasse C en D worden aangemaakt. Daarna klasse B en krijgt klasse C en D als parameters mee.
 *                                 Dan word klasse A gemaakt met klasse B als parameter.
 */


class DIContainer implements ContainerInterface
{
    public array $registeredClasses = [];
    public array $createdClasses = [];

    /**
     * Registers the way which class to use for certain id/interface/class name in constructors.
     *
     * @param string $id ID, or parameter type used in constructors or methods.
     * @param string $class Class name
     * @param array $config Configuration of manually defined parameters
     *
     * @return void
     */
    public function register(string $id, string $class, array $config = []): void
    {
        $this->registeredClasses[$id] = [
            "class" => $class,
            "config" => $config
        ];
    }

    public function quickRegister(string $class, array $config = []): void
    {
        $this->registeredClasses[$class] = [
            "class" => $class,
            "config" => $config
        ];
    }

    public function createClass(ReflectionClass $reflection): array
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
                    if (array_key_exists($paramName, $this->registeredClasses[$makingClass]["config"])) {
                        $params[$paramName] = $this->registeredClasses[$makingClass]["config"][$paramName];
                    } else {
                        throw new ContainerException("Unable to resolve " . $makingClass . " because no primitive parameter configuration was given on registration while class constructor requires one.");
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
                    } else {
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
            //throw new ContainerExceptionInterface("Service bestaat niet");
        }

        if (isset($this->createdClasses[$id])) return $this->createdClasses[$id];

        $reflection = new ReflectionClass($this->registeredClasses[$id]["class"]);
        $params = $this->createClass($reflection);
        $this->createdClasses[$id] = $reflection->newInstance(...$params);
        return $this->createdClasses[$id];
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