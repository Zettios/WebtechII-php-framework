<?php

namespace App\Controller\Admin;

use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class AdminController extends AbstractController
{
    #[Route(path: "/admin", name: "", method: "GET")]
    public static function adminHome(): string
    {
        return self::render("admin.html");
    }

    #[Route(path: "/admin/roles", name: "", method: "GET")]
    public static function adminRoles(): string
    {
        return self::render("test.html");
    }
}
