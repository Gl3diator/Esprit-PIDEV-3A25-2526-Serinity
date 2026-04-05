<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReveController extends AbstractController
{
    #[Route('/reve', name: 'app_reve')]
    public function index(): Response
    {
        return $this->render('reve/index.html.twig', [
            'controller_name' => 'ReveController',
        ]);
    }
}
