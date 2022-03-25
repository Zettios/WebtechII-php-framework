<?php

namespace vendor\autoloader;

spl_autoload_register(function ($className) {
    include dirname(__DIR__)."\\".$className.'.php';
});

$test = new \test\Class1();