<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MostListenedController extends AbstractController
{
    #[Route('/mostlistened', name: 'app_most_listened')]
    public function index(): Response {
        return $this->render('most_listened/index.html.twig', []);
    }
}
