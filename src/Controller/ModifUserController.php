<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ModifPasswordType;
use App\Form\ModifUserCoordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class ModifUserController extends AbstractController
{
    #[Route('/modif_user', name: 'app_modif_user')]
    public function index(): Response {
        return $this->render('modif_user/index.html.twig', []);
    }

    #[Route('/modifUserCoord', name: 'app_modif_user_coord')]
    public function modifUserCoord(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ModifUserCoordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /*$user->setLogin($form->get('login')->getData());
            $user->setNom($form->get('nom')->getData());
            $user->setPrenom($form->get('prenom')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setDateNaissance($form->get('date_naissance')->getData());
            $user->setAdresse($form->get('adresse')->getData());
            $user->setCodePostal($form->get('code_postal')->getData());
            $user->setVille($form->get('ville')->getData());
            $user->setTelephone($form->get('telephone')->getData());*/
   
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_modif_user');
        }

        return $this->render('security/modifUserCoord.html.twig', [
            'modifUserCoordForm' => $form
        ]);
    }

    #[Route('/modifUserMdp', name: 'app_modif_user_mdp')]
    public function modifUserMdp(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ModifPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();
            
            if (!$userPasswordHasher->isPasswordValid($user, $oldPassword)) {
                $form->addError(new FormError('Wrong old password'));
                return $this->render('security/modifUserMdp.html.twig', [
                    'modifPasswordForm' => $form
                ]);
            }
            
            if ($newPassword !== $confirmPassword) {
                $form->addError(new FormError("New passwords doesn't match."));
                return $this->render('security/modifyUserPassword.html.twig', [
                    'modifPasswordForm' => $form
                ]);
            }

            $mdp = $userPasswordHasher->hashPassword($user, $form->get('newPassword')->getData());
            $user->setPassword($mdp);
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_modif_user');
        }
        return $this->render('security/modifUserMdp.html.twig', [
            'modifPasswordForm' => $form
        ]);
    }

    #[Route('/userCoord', name: 'app_user_coord')]
    public function userCoord(): Response
    {
        $user = $this->getUser();

        return $this->render('security/userCoord.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/deleteUser', name: 'app_delete_user')]
    public function deleteUser(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) return $this->redirectToRoute('app_login');

        $entityManager->remove($user);
        $entityManager->flush();

        $this->container->get('security.token_storage')->setToken(null);

        return $this->redirectToRoute('app_home_page');
    }
}