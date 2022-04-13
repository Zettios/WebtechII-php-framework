<?php

namespace Webtek\Core;

class Kernel
{
    public string $uri;
    private string $method;
    private Router $router;
    private HttpFactory $factory;

    public function __construct()
    {
        $this->factory = new HttpFactory();
        $this->factory->makeObjects(1);


        $this->router = new Router();
        $this->uri = $_SERVER['REQUEST_URI']; //Uit een request halen
        $this->method = $_SERVER['REQUEST_METHOD']; //Uit een request halen
    }

    public function showPage()
    {
        $routes = json_decode(file_get_contents('../core/routes.json'), true);

        echo "<pre>";
        var_dump($this->uri);
        var_dump($this->method);
        var_dump($routes);
        var_dump($routes[$this->method]);
        echo "</pre>";

        if (array_key_exists($this->uri, $routes[$this->method])){
            $file = ($routes[$this->method][$this->uri]);
            include_once "../views/".$file.".html";
        } else {
            echo '404 not found';
        }
    }

    public function execute()
    {
        $this->showPage();
    }
}