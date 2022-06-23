<?php

namespace App\Controller\Admin;

use App\Entity\Users\User;
use Webtek\Core\Http\ServerRequest;
use Webtek\Core\Http\Response;
use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class AdminController extends AbstractController
{
    #[Route(path: "/admin", method: "GET", accessLevel: "2")]
    public static function adminHome(ServerRequest $request, User $user): array|Response
    {
        $cookies = $request->getCookieParams();

        if (!isset($cookies['id']) && !isset($cookies['accessRole'])) {
            return new Response('1.1', 403, textBody: 'Forbidden access');
        }

        $users = $user->getAllUsers();

        return self::render("admin.html", ['id' => $cookies['id'], 'role' => $cookies['accessRole'], 'users' => $users]);
    }
}
