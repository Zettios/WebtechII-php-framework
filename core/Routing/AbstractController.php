<?php

namespace Webtek\Core\Routing;

use Psr\Container\ContainerInterface;

class AbstractController
{
    protected $container;

    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $previous = $this->container;
        $this->container = $container;

        return $previous;
    }

}