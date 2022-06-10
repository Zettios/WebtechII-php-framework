<?php

namespace App\Controller;

use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class HomepageController extends AbstractController
{
    #[Route(path: "/", method: "GET")]
    public static function home(array $args): array
    {
        $title = "Awesome website!!";
        $name = "Enrico";
        $footer = "© Copyright 2022 by a new footer!";

        return self::render("homepage.html", ['title' => $title, 'footer' => $footer, 'name' => $name]);
    }
}
