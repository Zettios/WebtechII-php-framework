<?php

namespace App\Controller;

use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class HomepageController extends AbstractController
{
    #[Route("/", "GET")]
    public function home(){

    }
}
