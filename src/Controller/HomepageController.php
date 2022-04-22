<?php

namespace App\Controller;

use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class HomepageController extends AbstractController
{
    #[Route(path: "/", name: "", method: "GET")]
    public static function home(): string
    {
        return self::render("home.html");
    }
}
