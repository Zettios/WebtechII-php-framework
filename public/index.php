<?php

use Webtek\Core\Kernel;
use Webtek\Core\Request;

require_once dirname(__DIR__) . '/vendor/autoload.php';

//$test = new Request('1.1', [], null, 'GET', 'test.com');
//echo "<pre>";
//var_dump($_SERVER);
//echo "</pre>";

$kernel = new Kernel();
//$kernel->execute();

//$str = file_get_contents('../core/routes.json');
//var_dump($str);