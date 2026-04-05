<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SommeilController extends AbstractController
{
    #[Route('/sommeil', name: 'app_sommeil')]
    public function index(): Response
    {
        return $this->render('sommeil/index.html.twig', [
            'controller_name' => 'SommeilController',
        ]);
    }
}
