<?php

use Webtek\Core\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload.php';


//echo "<pre>";
//var_dump($_SERVER);
//echo "</pre>";

$kernel = new Kernel();
$kernel->execute();

//$str = file_get_contents('../core/routes.json');
//var_dump($str);