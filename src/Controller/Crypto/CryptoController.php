<?php

namespace App\Controller\Crypto;

use App\Entity\Crypto;
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
}