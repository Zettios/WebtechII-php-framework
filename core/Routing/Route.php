<?php

namespace Webtek\Core\Routing;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class Route
{

    public function __construct(private string $path, private string $method, private ?string $slugName = "", private string $accessLevel = "-1") {}

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getSlugName(): string
    {
        return $this->slugName;
    }

    /**
     * @param string $slugName
     */
    public function setSlugName(string $slugName): void
    {
        $this->slugName = $slugName;
    }

    /**
     * @return string
     */
    public function getAccessLevel(): string
    {
        return $this->accessLevel;
    }

    /**
     * @param string $accessLevel
     */
    public function setAccessLevel(string $accessLevel): void
    {
        $this->accessLevel = $accessLevel;
    }
}
