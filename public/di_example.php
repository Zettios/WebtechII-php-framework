<?php

use Webtek\Core\DependencyInjection\DIContainer;

require_once dirname(__DIR__) . '/vendor/autoload.php';

class A {
    public int $test = 100;
    public function __construct() {}
}

class B {
    public int $bla;

    public function __construct(A $a) {
        $this->bla = $a->test * 10;
    }
}

class C {
    public function __construct(B $b, string $reason) {
        echo 'B is ' . $b->bla . ' because ' . $reason;
    }
}

$di = new DIContainer();
$di->quickRegister(C::class, ["reason"=>"cause it is 100 multiplied by 10"]);
$di->quickRegister(B::class);
$di->quickRegister(A::class);



$newC = $di->get(C::class);
