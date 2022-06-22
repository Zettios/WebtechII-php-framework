<?php

namespace App\Controller\User;

use App\Entity\CryptoInWallet;
use App\Entity\Users\User;
use App\Entity\Wallet;
use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class UserController extends AbstractController
{
//    #[Route(path: "/user", method: "GET")]
//    public function userpage(User $user, Wallet $wallet, CryptoInWallet $cryptoInWallet): array
//    {
//        $currentUser = $user->getSpecificUser(1);
//        $usersWallet = $wallet->getSpecificWallet($currentUser["user_id"]);
//        $usersCrypto = $cryptoInWallet->getUsersCrypto($usersWallet["wallet_id"]);
//
//
//        return self::render("userpage.html", ['name' => $currentUser['name'], 'email'=>$currentUser['email'],
//                                                           'password'=>$currentUser['password'], 'wallet'=>$usersCrypto]);
//    }

    #[Route(path: "/user/{id}", method: "GET", slugName: "id", accessLevel: "1")]
    public function userAccountPage(User $user, Wallet $wallet, CryptoInWallet $cryptoInWallet, string $id): array
    {
        $currentUser = $user->getSpecificUser(intval($id));
        $usersWallet = $wallet->getSpecificWallet($currentUser["user_id"]);
        $usersCrypto = $cryptoInWallet->getUsersCrypto($usersWallet["wallet_id"]);


        return self::render("userpage.html", ['name' => $currentUser['name'], 'email'=>$currentUser['email'],
            'password'=>$currentUser['password'], 'role' => $currentUser['role'], 'wallet'=>$usersCrypto]);
    }
}