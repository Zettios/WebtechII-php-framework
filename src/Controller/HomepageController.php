<?php

namespace Webtek\Controllers;

use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class HomepageController extends AbstractController
{
    #[Route(path: "/", method: "GET")]
    public static function home(): string
    {
        return self::render("home.html");
    }
}
