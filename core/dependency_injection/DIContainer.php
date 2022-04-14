<?php

namespace Webtek\Core\DependencyInjection;

use http\Exception\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

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

        return 0;
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

    public function set(string $id, callable $callable){
        $this->services[$id] = $callable;
    }
}