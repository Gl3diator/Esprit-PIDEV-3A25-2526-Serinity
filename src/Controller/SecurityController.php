<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, EntityManagerInterface $em): Response
    {
        $error = null;

        if ($request->isMethod('POST')) {

            $email = strtolower(trim($request->request->get('email')));
            $password = trim($request->request->get('password'));

            $user = $em->getRepository(User::class)->findOneBy([
                'email' => $email
            ]);

            if (!$user) {
                $error = "Utilisateur introuvable";
            } elseif (!$user->getPassword() || $user->getPassword() != $password) {
                $error = "Mot de passe incorrect";
            } else {
                $request->getSession()->set('user_name', $user->getFullName());
                $request->getSession()->set('user_email', $user->getEmail());
                $request->getSession()->set('user_id', $user->getId());

                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('security/login.html.twig', [
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request)
    {
        $request->getSession()->invalidate();
        return $this->redirectToRoute('app_login');
    }
}