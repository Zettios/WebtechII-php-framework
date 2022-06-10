<?php

namespace App\Controller\Admin;

use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class AdminController extends AbstractController
{
    #[Route(path: "/base", method: "GET")]
    public static function adminHome(): array
    {
        $title = "Awesome website!!";
        $name = "Admin";
        $test = "This is a test argument!";

        return self::render("admin.html", ['title' => $title, 'name' => $name, 'test' => $test]);
    }
}
