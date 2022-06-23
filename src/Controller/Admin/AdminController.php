<?php

namespace App\Controller\Admin;

use App\Entity\Roles;
use App\Entity\Users\User;
use Webtek\Core\Http\ServerRequest;
use Webtek\Core\Http\Response;
use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class AdminController extends AbstractController
{
    #[Route(path: "/admin", method: "GET", accessLevel: "2")]
    public static function adminHome(ServerRequest $request, User $user, Roles $roles): array|Response
    {
        $cookies = $request->getCookieParams();

        if (!isset($cookies['id']) && !isset($cookies['accessRole'])) {
            return new Response('1.1', 403, textBody: 'Forbidden access');
        }

        $users = $user->getAllUsers();

        return self::render("admin.html", ['id' => $cookies['id'], 'role' => $cookies['accessRole'],
                                                        'users' => $users]);
    }

    #[Route(path: "/updateUserAdmin", method: "PUT", accessLevel: "2")]
    public function updateUserAdmin(ServerRequest $request, User $user): Response
    {
        $params = $request->getQueryParams();
        $id = $params['user_id'];
        $username = $params["username"];
        $email = $params["email"];
        $password = $params["password"];
        $role = $params["role"];

        $cookies = $request->getCookieParams();
        if (!isset($cookies['id']) && !isset($cookies['accessRole'])) {
            return new Response('1.1', 404, textBody: '{"status": 404, "message": "User not found"}');
        }

        if ($user->checkUsernameAdmin($id, $username)) {
            $user->updateUserAdmin($id, $username, $email, $password, $role);
            return new Response('1.1', 200, textBody: '{"status": 200, "message": "Success"}');
        }

        return new Response('1.1', 403, textBody: '{"status": 403, "message": "Username already exists"}');
    }
}
