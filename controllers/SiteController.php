<?php

namespace Webtek\Controllers;

use Webtek\Core\Application;
use Webtek\Core\Controller;
use Webtek\Core\Request;

class SiteController extends Controller {
    public function home(): string{
        $params = [
            'name' => 'Test'
        ];
        return $this->render('home', $params);
    }

    public function contact(): string{
        return $this->render('contact');
    }

    public function handleContact(Request $request): string{
        $body = $request->getBody();
        return 'Handling submitted data';
    }
}