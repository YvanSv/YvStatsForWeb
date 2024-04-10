<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MusicController extends AbstractController
{
    #[Route('/create_music', name: 'app_create_music')]
    public function create(): Response {
        return $this->render('music/index.html.twig', []);
    }
}
