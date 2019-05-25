<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController
{

    /**
     * @Route("/admin/test", name="admin_test")
     */
    public function test()
    {

        return new Response('test');
    }
}