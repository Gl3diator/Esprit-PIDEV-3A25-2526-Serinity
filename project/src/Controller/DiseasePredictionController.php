<?php

namespace App\Controller;

use App\Controller\User\AbstractUserUiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DiseasePredictionController extends AbstractUserUiController
{
    #[Route('/user/rdv/disease-ai', name: 'app_rdv_disease_ai', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->currentUser();

        return $this->render('rdv/rdv_disease.html.twig', [
            'nav' => $this->buildNav('app_rdv_disease_ai'),
            'userName' => $user->getEmail(),
        ]);
    }
}