<?php

use Webtek\Core\Kernel;
use Webtek\Core\Request;

require_once dirname(__DIR__) . '/vendor/autoload.php';

echo "<pre>";
var_dump($_SERVER);
echo "</pre>";

$kernel = new Kernel();
//$kernel->execute();