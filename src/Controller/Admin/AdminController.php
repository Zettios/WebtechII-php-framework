<?php

namespace App\Controller\Admin;

use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class AdminController extends AbstractController
{
    #[Route(path: "/base", method: "GET", accessLevel: "2")]
    public static function adminHome(): array
    {
        return [];
    }
}
