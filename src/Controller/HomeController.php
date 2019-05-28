<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{

    /**
     * Home page.
     * @Route("/", name="app_homepage")
     *
     * @return Response
     */
    public function home(): Response
    {
        return $this->render('pages/home.html.twig');
    }
}