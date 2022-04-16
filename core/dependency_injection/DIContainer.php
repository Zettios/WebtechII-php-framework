<?php

namespace Webtek\Core\DependencyInjection;

use http\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;

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

    public function register(string $id, string $class): void
    {
        $this->registeredClasses[$id] = $class;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new InvalidArgumentException("Service bestaat niet");
            //throw new ContainerExceptionInterface("Service bestaat niet");
        }

        $reflection = new ReflectionClass($this->registeredClasses[$id]);
        $params = $this->createClass($reflection);
        return $reflection->newInstance(...$params);
    }

    public function createClass(ReflectionClass $reflection): mixed
    {
        $cons = $reflection->getConstructor();
        $params = [];

        if (isset($cons)) {
            foreach ($cons->getParameters() as $param) {
                $name = $param->getName();
                $type = $param->getType();
                echo "Name: ".$name."<br>Type: ".$type."<br><br>";
                if ($type instanceof ReflectionNamedType) {
                    $reflectionClass = new ReflectionClass($type->getName());
                    $params[$name] = $this->createClass($reflectionClass);
                }
            }
        } else {
            echo $reflection->getName();
            return $reflection->newInstance();
        }

        return $params;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        if (!array_key_exists($id, $this->registeredClasses)){
            return false;
        }

        return true;
    }
}