<?php

namespace Webtek\Core;

use Webtek\Core\DependencyInjection\Container;
use Webtek\Core\RequestHandling\HttpFactory;
use Webtek\Core\RequestHandling\ServerRequest;

class Kernel
{
    private ServerRequest $request;
    private Container $container;

    public function __construct()
    {
        $this->request = new ServerRequest();
        //echo $this->request->getUri()->__toString();
        $this->container = new Container();
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