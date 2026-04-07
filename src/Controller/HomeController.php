<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em ): Response
    {

        $userId = $request->getSession()->get('user_id');


     



        $user = $em->getRepository(User::class)->find($userId);

        if (!$userId) {
            return $this->redirectToRoute('app_login');
        }
        if (!$user->getSpeciality() == null) {
            return $this->redirectToRoute('app_dashboard');
        }


        return $this->render('home/index.html.twig', [
            'currentUser' => $user
        ]);
    }
}
