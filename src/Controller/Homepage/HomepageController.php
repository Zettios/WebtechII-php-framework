<?php

namespace App\Controller\Homepage;

use App\Entity\Users\User;
use App\Entity\Wallet;
use stdClass;
use Webtek\Core\Http\Request;
use Webtek\Core\Http\Response;
use Webtek\Core\Http\ServerRequest;
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

    #[Route(path: "/registerUser", method: "POST")]
    public static function registerUser(ServerRequest $request, User $user): Response
    {
        $params = $request->getQueryParams();
        $email = $params["email"];
        $username = $params["username"];
        $password = $params["password"];

        $user->registerUser($username, $email, $password);

        $jsonString = '{"email":"'.$email.'","username":"'.$username.'","password":"'.$password.'"}';

        return new Response('1.1', 200, textBody: "Success");
    }
}
