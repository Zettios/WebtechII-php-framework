<?php

namespace App\Controller\Crypto;

use App\Entity\Crypto;
use App\Entity\Users\User;
use Webtek\Core\Http\Response;
use Webtek\Core\Http\ServerRequest;
use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class CryptoController extends AbstractController
{

    #[Route(path: "/crypto", method: "GET", accessLevel: "1")]
    public function crypto(Crypto $crypto, ServerRequest $request): array|Response
    {
        $allCrypto = $crypto->getAllCrypto();

        $cookies = $request->getCookieParams();

        if (!isset($cookies['id']) && !isset($cookies['accessRole'])) {
            return new Response('1.1', 403, textBody: 'Forbidden access');
        }

        return self::render("overview.html", ['crypto' => $allCrypto, 'id' => $cookies['id'], 'role' => $cookies['accessRole']]);
    }

    #[Route(path: "/crypto/buy/{id}", method: "GET", slugName: "id", accessLevel: "1")]
    public function buyCrypto(Crypto $crypto, User $user, ServerRequest $request, string $id): array|Response
    {
        $crypto = $crypto->getSingleCrypto(intval($id));

        $cookies = $request->getCookieParams();

        if (!isset($cookies['id']) && !isset($cookies['accessRole'])) {
            return new Response('1.1', 403, textBody: 'Forbidden access');
        }

        $currentUserWallet = $user->getWallet($cookies['id'], $crypto['crypto_id']);

        if (count($currentUserWallet) === 0) {
            return self::render("no_wallet.html", ['crypto_name' => $crypto['name'],
                                                                'id' => $cookies['id'], 'role' => $cookies['accessRole']]);
        }

        return self::render("buy.html", ['crypto_name' => $crypto['name'], 'crypto_value' => $crypto['value'],
                                                      'id' => $cookies['id'], 'role' => $cookies['accessRole'],
                                                      'yourAmount' => $currentUserWallet['amount']]);
    }

    #[Route(path: "/boughtCrypto", method: "PUT", accessLevel: "1")]
    public static function boughtCrypto(ServerRequest $request, User $user, Crypto $crypto): Response
    {
        $params = $request->getQueryParams();
        $crypto_id = $params["crypto_id"];
        $amountToBuy = $params["buy_amount"];

        $cookies = $request->getCookieParams();
        if (!isset($cookies['id']) && !isset($cookies['accessRole'])) {
            return new Response('1.1', 404, textBody: '{"status": 404, "message": "User not found"}');
        }

        $id = $cookies['id'];
        $currentCrypto = $crypto->getSingleCrypto($crypto_id);
        $wallet = $user->getWallet($id, $crypto_id);

        if ($amountToBuy > 0.00000 && $amountToBuy <= $currentCrypto['value']) {
            $crypto->updateCryptoValue($crypto_id, $currentCrypto['value']-$amountToBuy);
            $crypto->updateWallet($wallet['wallet_id'], $crypto_id, $wallet['amount']+$amountToBuy);
            return new Response('1.1', 200, textBody: '{"status": 200, "message": "Success"}');
        }

        return new Response('1.1', 403, textBody: '{"status": 404, "message": "Failed"}');
    }

    #[Route(path: "/crypto/sell/{id}", method: "GET", slugName: "id", accessLevel: "1")]
    public function sellCrypto(Crypto $crypto, User $user, ServerRequest $request, string $id): array|Response
    {
        $crypto = $crypto->getSingleCrypto(intval($id));
        $cookies = $request->getCookieParams();

        if (!isset($cookies['id']) && !isset($cookies['accessRole'])) {
            return new Response('1.1', 403, textBody: 'Forbidden access');
        }

        $currentUserWallet = $user->getWallet($cookies['id'], $crypto['crypto_id']);

        if (count($currentUserWallet) === 0) {
            return self::render("no_wallet.html", ['crypto_name' => $crypto['name'],
                'id' => $cookies['id'], 'role' => $cookies['accessRole']]);
        }

        return self::render("sell.html", ['crypto_name' => $crypto['name'], 'crypto_value' => $crypto['value'],
                                                      'id' => $cookies['id'], 'role' => $cookies['accessRole'],
                                                      'yourAmount' => $currentUserWallet['amount']]);
    }

    #[Route(path: "/soldCrypto", method: "PUT", accessLevel: "1")]
    public static function soldCrypto(ServerRequest $request, User $user, Crypto $crypto): Response
    {
        $params = $request->getQueryParams();
        $crypto_id = $params["crypto_id"];
        $amountToSell = $params["sell_amount"];

        $cookies = $request->getCookieParams();
        if (!isset($cookies['id']) && !isset($cookies['accessRole'])) {
            return new Response('1.1', 404, textBody: '{"status": 404, "message": "User not found"}');
        }

        $id = $cookies['id'];
        $currentCrypto = $crypto->getSingleCrypto($crypto_id);
        $wallet = $user->getWallet($id, $crypto_id);

        if ($amountToSell > 0.00000 && $amountToSell <= $wallet['amount']) {
            $crypto->updateCryptoValue($crypto_id, $currentCrypto['value']+$amountToSell);
            $crypto->updateWallet($wallet['wallet_id'], $crypto_id, $wallet['amount']-$amountToSell);
            return new Response('1.1', 200, textBody: '{"status": 200, "message": "Success"}');
        }

        return new Response('1.1', 403, textBody: '{"status": 404, "message": "Failed"}');
    }

}