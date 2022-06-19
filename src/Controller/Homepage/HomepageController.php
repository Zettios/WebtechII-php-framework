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

    #[Route(path: "/loginUser", method: "GET")]
    public static function loginUser(ServerRequest $request, User $user): Response
    {
        $params = $request->getQueryParams();
        $username = $params["username"];
        $password = $params["password"];

        //$password = password_hash($password,PASSWORD_BCRYPT );

        $statusCode = $user->loginUser($username, $password);
        if ($statusCode["status"] === 200) {
            return new Response('1.1', $statusCode["status"], textBody: '{"status":"'.$statusCode["status"].'","message": "Success", "id": "'.$statusCode["id"].'"}');
        } else {
            return new Response('1.1', $statusCode["status"], textBody:  '{"status":"'.$statusCode["status"].'","message": "User not found"}');
        }
    }

    #[Route(path: "/registerUser", method: "POST")]
    public static function registerUser(ServerRequest $request, User $user): Response
    {
        $params = $request->getQueryParams();
        $email = $params["email"];
        $username = $params["username"];
        $password = $params["password"];

        $password = password_hash($password,PASSWORD_BCRYPT );

        $user->registerUser($username, $email, $password);

        return new Response('1.1', 200, textBody: '{"200": "Success"}');
    }
}
