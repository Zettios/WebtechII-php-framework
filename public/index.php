<?php

use Webtek\Core\Request;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$request = (new Webtek\Core\Request)->getUri();

var_dump($request['REQUEST_URI']);