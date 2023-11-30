<?php

namespace App\Controller\Frontend;

use App\Constants\RouteConstants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct
    (
    )
    {
    }

    #[Route('', name: RouteConstants::ROUTE_HOME)]
    public function index(): Response
    {
        return $this->render('frontend/home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}