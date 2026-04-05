<?php

namespace App\Controller;

use App\Service\CurrentUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CurrentUserService $currentUserService): Response
    {
        $currentUser = $currentUserService->requireUser();
        if ($currentUserService->isAdmin($currentUser)) {
            return $this->redirectToRoute('app_admin_forum');
        }

        return $this->redirectToRoute('app_forum_feed');
    }
}
