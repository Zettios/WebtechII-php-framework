<?php

namespace App\Controller;

use App\Entity\Users\User;
use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class HomepageController extends AbstractController
{

    #[Route(path: "/", method: "GET")]
    #[Property(type: 'function', name: 'Hello')]
    public static function home(User $user, array $args): array
    {

        //$user = HomepageController::class->getUser()->getAllUsers();

        $title = "Awesome website!!";
        $name = "Enrico";
        $footer = "© Copyright 2022 by a new footer!";

        return self::render("homepage.html", ['title' => $title, 'footer' => $footer, 'name' => $name]);
    }
}
