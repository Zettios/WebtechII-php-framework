<?php

namespace App\Controller\Crypto;

use App\Entity\Crypto;
use Webtek\Core\Http\ServerRequest;
use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class CryptoController extends AbstractController
{

    #[Route(path: "/crypto", method: "GET")]
    public function crypto(Crypto $crypto, ServerRequest $request): array
    {
        $allCrypto = $crypto->getAllCrypto();
//        echo "<pre>";
//        //print_r($request->getServerParams());
//        print_r($request->getCookieParams());
//        echo "</pre>";

        $cookies = $request->getCookieParams();

        return self::render("overview.html", ['crypto' => $allCrypto, 'id' => $cookies['id'], 'role' => $cookies['role']]);
    }
}