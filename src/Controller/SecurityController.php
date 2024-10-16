<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, on le redirige en fonction de son rôle
        if ($this->getUser()) {
            return $this->redirectToRoute($this->getRedirectRouteBasedOnRole($this->getUser()));
        }

        // Créer le formulaire de connexion
        $form = $this->createForm(LoginFormType::class, [
            'email' => $authenticationUtils->getLastUsername(), // Dernier email saisi
        ]);

        // Gestion des erreurs de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('Pages/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): RedirectResponse
    {
        return $this->redirect('home');
    }

    // Cette méthode renvoie la route en fonction du rôle de l'utilisateur
    private function getRedirectRouteBasedOnRole($user): string
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return 'admin_home';
        }

        if ($this->isGranted('ROLE_VOLUNTEER')) {
            return 'volunteer_home';
        }

        if ($this->isGranted('ROLE_EXHIBITOR')) {
            return 'exhibitor_home';
        }

        return 'home';
    }
}