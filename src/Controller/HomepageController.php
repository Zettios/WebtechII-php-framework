<?php

namespace App\Controller;

use App\Entity\Users\User;
use App\Entity\Wallet;
use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class HomepageController extends AbstractController
{

    #[Route(path: "/", method: "GET")]
    public function home(User $user, Wallet $wallet): array
    {

        $person = $user->getAllUsers();

        $title = "Awesome website!!";
        $name = $person['name'];
        $footer = "© Copyright 2022 BitTraders";

        return self::render("login.html", [  'title' => $title, 'footer' => $footer,
                                                            'user_id' => $person['user_id'],
                                                            'name' => $name, 'email'=>$person['email'],
                                                            'password'=>$person['password'], 'role' => $person['role']]);
    }

    #[Route(path: "/register", method: "GET")]
    public static function user(): array
    {

        return self::render("register.html", []);
    }
}
