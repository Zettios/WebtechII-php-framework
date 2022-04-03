<?php

spl_autoload_register('autoLoader');

function autoLoader($className) {
    $extension = ".php";
    $fullPath = $className . $extension;
    echo $fullPath;

    include_once $fullPath;
}