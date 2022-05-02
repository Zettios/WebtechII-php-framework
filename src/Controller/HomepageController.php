<?php

namespace App\Controller;

use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class HomepageController extends AbstractController
{
    #[Route(path: "/", method: "GET")]
    public static function home(array $args): string
    {
        echo "<pre>";
        print_r($args);
        echo "</pre>";
        return self::render("home.html");
    }
}
