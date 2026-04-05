<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use App\Service\CurrentUserService;
use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/forum')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_forum')]
    public function index(CategoryRepository $categoryRepository, CurrentUserService $currentUserService): Response
    {
        if (($redirect = $this->redirectIfNotAdmin($currentUserService)) instanceof Response) {
            return $redirect;
        }

        $currentUser = $currentUserService->requireUser();

        return $this->render('admin/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'currentUser' => $currentUser,
        ]);
    }

    #[Route('/categories/new', name: 'app_admin_category_new')]
    public function newCategory(Request $request, CategoryService $categoryService, CurrentUserService $currentUserService): Response
    {
        if (($redirect = $this->redirectIfNotAdmin($currentUserService)) instanceof Response) {
            return $redirect;
        }

        $currentUser = $currentUserService->requireUser();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryService->save($category);

            return $this->redirectToRoute('app_admin_forum');
        }

        return $this->render('admin/category_form.html.twig', [
            'form' => $form,
            'mode' => 'create',
            'currentUser' => $currentUser,
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'app_admin_category_edit')]
    public function editCategory(Category $category, Request $request, CategoryService $categoryService, CurrentUserService $currentUserService): Response
    {
        if (($redirect = $this->redirectIfNotAdmin($currentUserService)) instanceof Response) {
            return $redirect;
        }

        $currentUser = $currentUserService->requireUser();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryService->save($category);

            return $this->redirectToRoute('app_admin_forum');
        }

        return $this->render('admin/category_form.html.twig', [
            'form' => $form,
            'mode' => 'edit',
            'currentUser' => $currentUser,
        ]);
    }

    #[Route('/categories/{id}/delete', name: 'app_admin_category_delete', methods: ['POST'])]
    public function deleteCategory(Category $category, CategoryService $categoryService, CurrentUserService $currentUserService): Response
    {
        if (($redirect = $this->redirectIfNotAdmin($currentUserService)) instanceof Response) {
            return $redirect;
        }

        $categoryService->delete($category);

        return $this->redirectToRoute('app_admin_forum');
    }

    #[Route('/statistics', name: 'app_admin_statistics')]
    public function statistics(StatisticsService $statisticsService, CurrentUserService $currentUserService): Response
    {
        if (($redirect = $this->redirectIfNotAdmin($currentUserService)) instanceof Response) {
            return $redirect;
        }

        $currentUser = $currentUserService->requireUser();

        return $this->render('admin/statistics.html.twig', [
            'stats' => $statisticsService->getForumStatistics(),
            'currentUser' => $currentUser,
        ]);
    }

    private function redirectIfNotAdmin(CurrentUserService $currentUserService): ?Response
    {
        $currentUser = $currentUserService->requireUser();
        if (!$currentUserService->isAdmin($currentUser)) {
            return $this->redirectToRoute('app_forum_feed');
        }

        return null;
    }
}
