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

        $response = $user->loginUser($username, $password);
        if ($response["status"] === 200) {
            setcookie('id', $response["id"]);
            setcookie('accessRole', $response["role"]);
            return new Response('1.1', $response["status"], textBody: '{"status":'.$response["status"].',"message": "Success", "id": '.$response["id"].'}');
        } else {
            return new Response('1.1', $response["status"], textBody:  '{"status":'.$response["status"].',"message": "User not found"}');
        }
    }

    #[Route(path: "/registerUser", method: "POST")]
    public static function registerUser(ServerRequest $request, User $user): Response
    {
        $params = $request->getQueryParams();
        $email = $params["email"];
        $username = $params["username"];
        $password = $params["password"];

        $password = password_hash($password,PASSWORD_BCRYPT);

        $statusCode = $user->registerUser($username, $email, $password);

        if ($statusCode["status"] === 201) {
            return new Response('1.1', $statusCode["status"], textBody: '{"status":'.$statusCode["status"].',"message": "Account created"}');
        } else {
            return new Response('1.1', $statusCode["status"], textBody:  '{"status":'.$statusCode["status"].',"message": "Username already exists"}');
        }
    }
}
