<?php

namespace App\Controller;

use App\Entity\AccesToken;
use App\Repository\AccesTokenRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomePageController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    #[Route('/', name: 'app_home_page')]
    public function index(AccesTokenRepository $atrep): Response {
        $token = $atrep->findOneBy([]);
        if (!$token) {
            $token = new AccesToken();
            $this->doctrine->getManager()->persist($token);
            $this->doctrine->getManager()->flush();
        }
        return $this->render('home_page/home.html.twig', []);
    }
}
