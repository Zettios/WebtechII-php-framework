<?php

namespace App\Controller\User;

use App\Entity\CryptoInWallet;
use App\Entity\Users\User;
use App\Entity\Wallet;
use Webtek\Core\Http\Response;
use Webtek\Core\Http\ServerRequest;
use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class UserController extends AbstractController
{
    #[Route(path: "/user/{id}", method: "GET", slugName: "id", accessLevel: "1")]
    public function userAccountPage(User $user, Wallet $wallet, CryptoInWallet $cryptoInWallet, string $id): array
    {
        $currentUser = $user->getSpecificUser(intval($id));
        $usersWallet = $wallet->getSpecificWallet($currentUser["user_id"]);
        $usersCrypto = $cryptoInWallet->getUsersCrypto($usersWallet["wallet_id"]);


        return self::render("userpage.html", ['name' => $currentUser['name'], 'email'=>$currentUser['email'],
            'password'=>$currentUser['password'], 'role' => $currentUser['role'], 'wallet'=>$usersCrypto, 'id' => $currentUser["user_id"]]);
    }

    #[Route(path: "/user/edit/{id}", method: "GET", slugName: "id", accessLevel: "1")]
    public function editUserPage(User $user, string $id): array
    {
        $currentUser = $user->getSpecificUser(intval($id));

        return self::render("edit.html", ['id' => $currentUser["user_id"], 'name' => $currentUser['name'], 'email'=>$currentUser['email'],
            'password'=>$currentUser['password'], 'role' => $currentUser['role']]);
    }

    #[Route(path: "/updateUser", method: "PUT", accessLevel: "1")]
    public function updateUser(ServerRequest $request, User $user): Response
    {
        $params = $request->getQueryParams();
        $username = $params["username"];
        $email = $params["email"];
        $password = $params["password"];

        $cookies = $request->getCookieParams();
        if (!isset($cookies['id']) && !isset($cookies['accessRole'])) {
            return new Response('1.1', 404, textBody: '{"status": 404, "message": "User not found"}');
        }

        if (is_bool($user->checkUsername($username))) {
            $password = password_hash($password,PASSWORD_BCRYPT);
            $user->updateUser($cookies['id'], $username, $email, $password);
            return new Response('1.1', 200, textBody: '{"status": 200, "message": "Updated"}');
        }

        return new Response('1.1', 403, textBody: '{"status": 403, "message": "Username already exists"}');
    }
}