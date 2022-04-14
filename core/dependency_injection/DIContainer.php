<?php

namespace Webtek\Core\DependencyInjection;

use Exception;
use http\Exception\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;

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
    public array $services = [];

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id)
    {
        if (!$this->has($id)){
            throw new InvalidArgumentException("Service bestaat niet");
        }

        $service = $this->services[$id];
        return $service($this);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        if (!array_key_exists($id, $this->services)){
            return false;
        }

        return true;
    }

    public function resolve($service)
    {
        $reflection = new ReflectionClass($service);
        if (!$reflection->isInstantiable()){
            throw new Exception("Class $service is not instantiable");
        }

        $constructor = $reflection->getConstructor();
        if (is_null($constructor)) {
            return $reflection->newInstance();
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters, $reflection);


        echo "<pre>";
        echo $constructor;
        echo "</pre>";

        return $reflection->newInstanceArgs($dependencies);
    }

    private function getDependencies($parameters, $reflection): array
    {
        $dependencies = [];
        foreach ($parameters as $parameter){
            $dependency = $parameters->getType();
        }
        return [];
    }

    public function set(string $id, callable $callable): void{
        $this->services[$id] = $callable;
    }
}