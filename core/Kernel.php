<?php

namespace Webtek\Core;

use Webtek\Core\RequestHandling\HttpFactory;
use Webtek\Core\RequestHandling\ServerRequest;
use Webtek\Core\Routing\Router;

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
        echo "\n========= Server request methodes =========";
        echo "\n- getServerParams -\n";
        print_r($request->getServerParams());
        echo "\n- getCookieParams -\n";
        print_r($request->getCookieParams());
        echo "\n- getQueryParams -\n";
        print_r($request->getQueryParams());
        echo "\n- getUploadedFiles -\n";
        print_r($request->getUploadedFiles());
        echo "\n- getParsedBody -\n";
        print_r($request->getParsedBody());
        echo "\n- getAttributes -\n";
        print_r($request->getAttributes());

        echo "\n\n\n========= Request methodes =========";
        echo "\n - getMethod -\n";
        echo $request->getMethod();
        echo "\n - getRequestTarget -\n";
        echo $request->getRequestTarget();
        echo "\n - getUri -\n";
        echo $request->getUri();

        echo "\n\n\n========= Uri methodes =========";
        echo "\n- getScheme -\n";
        echo $request->getUri()->getScheme();
        echo "\n- getUserInfo -\n";
        echo $request->getUri()->getUserInfo();
        echo "\n- getHost -\n";
        echo $request->getUri()->getHost();
        echo "\n- getPort -\n";
        echo $request->getUri()->getPort();
        echo "\n- getAuthority -\n";
        echo $request->getUri()->getAuthority();
        echo "\n- getPath -\n";
        echo $request->getUri()->getPath();
        echo "\n- getQuery -\n";
        echo $request->getUri()->getQuery();
        echo "\n- getFragment -\n";
        echo $request->getUri()->getFragment();
        echo "\n- __toString -\n";
        echo $request->getUri()->__toString();

        echo "\n\n\n========= MessageTrait methodes =========\n";
        echo "\n- getProtocolVersion -\n";
        echo $request->getProtocolVersion();
        echo "\n- getHeaders -\n";
        print_r($request->getHeaders());
        echo "\n- getBodyAsArray -\n";
        print_r($request->getBodyAsArray());


        echo "\n\n\n========= With methodes test =========\n";
        echo "\n- withProtocolVersion 1.0 -\n";
        $request = $request->withProtocolVersion('1.0');
        echo $request->getProtocolVersion();

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