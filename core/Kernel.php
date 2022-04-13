<?php

namespace Webtek\Core;

class Kernel
{
    public string $uri;
    private string $method;
    private Router $router;
    private ServerRequest $request;
    private HttpFactory $factory;

    public function __construct()
    {
        $this->factory = new HttpFactory();
        $request = $this->factory->makeObjects(HttpFactory::REQUEST);

        echo "<pre>";
        print_r($request->getServerParams());
        echo $request->getUri()->__toString();
        echo "</pre>";


        $this->router = new Router();
        $this->uri = $request->getUri(); //Uit een request halen
        $this->method = $request->getMethod(); //Uit een request halen
    }

    public function showPage()
    {
        $routes = json_decode(file_get_contents('../core/routes.json'), true);

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