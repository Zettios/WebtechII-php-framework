<?php

namespace App\Controller\Homepage;

use App\Entity\Users\User;
use App\Entity\Wallet;
use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class HomepageController extends AbstractController
{

    #[Route(path: "/", method: "GET")]
    public function home(User $user, Wallet $wallet): array
    {
        return self::render("login.html", []);
    }

    #[Route(path: "/register", method: "GET")]
    public static function user(): array
    {

        return self::render("register.html", []);
    }
}
