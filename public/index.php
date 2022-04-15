<?php

use Webtek\Core\DependencyInjection\DIContainer;
use Webtek\Core\Kernel;
use Webtek\Core\RequestHandling\ServerRequest;
use Webtek\Core\Routing\A;

require_once dirname(__DIR__) . '/vendor/autoload.php';

//echo "<pre>";
//var_dump($_SERVER);
//echo "</pre>";


$request = new ServerRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
$di = new DIContainer();
$di->set('aClass', [A::class, 'run']);
$service = $di->get('aClass');


////============ TEST FOR DI CONTAINER ============
//class A {}
//class B {}
//class Test {
//    public function __construct(
//        public A $a, public B $b) {}
//}
//
//$ref = new ReflectionClass(Test::class);
//$cons = $ref->getConstructor();
//$params = [];
//foreach ($cons->getParameters() as $param) {
//    $name = $param->getName();
//    $type = $param->getType();
//    echo "Name is $name, type is $type<br>";
//    if ($type instanceof ReflectionNamedType) {
//        $cls = new ReflectionClass($type->getName());
//        $params[$name] = $cls->newInstance();
//    } else {
//        $params[$name] = null;
//    }
//}
//$test = $ref->newInstance(...$params);
//echo "<pre>";
//var_dump($test);
//echo "</pre>";
////============ END TEST FOR DI CONTAINER ============

//$this->printRequest($request);

function printRequest(ServerRequest $request)
{
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
}