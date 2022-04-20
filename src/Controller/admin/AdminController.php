<?php

namespace App\Controller\admin;

use Webtek\Core\Routing\AbstractController;
use Webtek\Core\Routing\Route;

class AdminController extends AbstractController
{
    #[Route("/", "GET")]
    public function home(){

    }
}
