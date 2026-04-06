<?php

namespace App\Controller;

use App\Entity\ForumThread;
use App\Enum\ThreadStatus;
use App\Form\ForumThreadType;
use App\Service\CurrentUserService;
use App\Service\ImageUploadService;
use App\Service\InteractionService;
use App\Service\ThreadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/threads')]
class ThreadManageController extends AbstractController
{
    #[Route('/new', name: 'app_thread_new')]
    public function new(Request $request, ThreadService $threadService, CurrentUserService $currentUserService, ImageUploadService $uploadService): Response
    {
        $currentUser = $currentUserService->requireUser();
        if ($currentUserService->isAdmin($currentUser)) {
            return $this->redirectToRoute('app_admin_forum');
        }

        $threadError = null;
        $thread = new ForumThread();
        $form = $this->createForm(ForumThreadType::class, $thread);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $thread->setAuthorId($currentUser->getId());
            $image = $form->get('imageFile')->getData();
            $thread->setImageUrl($uploadService->upload($image));

            try {
                $threadService->saveThread($thread);

                return $this->redirectToRoute('app_forum_feed');
            } catch (\RuntimeException $exception) {
                $this->addFlash('danger', $exception->getMessage());
                $threadError = $exception->getMessage();
            }
        }

        return $this->render('thread/form.html.twig', [
            'form' => $form,
            'mode' => 'create',
            'currentUser' => $currentUser,
            'threadError' => $threadError,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_thread_edit', requirements: ['id' => '\\d+'])]
    public function edit(ForumThread $thread, Request $request, ThreadService $threadService, CurrentUserService $currentUserService): Response
    {
        $currentUser = $currentUserService->requireUser();
        if ($currentUserService->isAdmin($currentUser)) {
            return $this->redirectToRoute('app_admin_forum');
        }

        $threadError = null;
        $this->denyUnlessCanManage($thread, $threadService, $currentUserService);

        $form = $this->createForm(ForumThreadType::class, $thread);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $threadService->saveThread($thread);

                return $this->redirectToRoute('app_forum_thread_detail', ['id' => $thread->getId()]);
            } catch (\RuntimeException $exception) {
                $this->addFlash('danger', $exception->getMessage());
                $threadError = $exception->getMessage();
            }
        }

        return $this->render('thread/form.html.twig', [
            'form' => $form,
            'mode' => 'edit',
            'thread' => $thread,
            'currentUser' => $currentUser,
            'threadError' => $threadError,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_thread_delete', methods: ['POST'])]
    public function delete(ForumThread $thread, Request $request, ThreadService $threadService, CurrentUserService $currentUserService): Response
    {
        if ($currentUserService->isAdmin($currentUserService->requireUser())) {
            return $this->redirectToRoute('app_admin_forum');
        }

        $this->denyUnlessCanManage($thread, $threadService, $currentUserService);

        if (!$this->isCsrfTokenValid('delete_thread_'.$thread->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid delete token.');
        }

        $threadService->deleteThread($thread);

        return $this->redirectToRoute('app_forum_feed');
    }

    #[Route('/{id}/status/{status}', name: 'app_thread_status')]
    public function status(ForumThread $thread, string $status, ThreadService $threadService, CurrentUserService $currentUserService): Response
    {
        if ($currentUserService->isAdmin($currentUserService->requireUser())) {
            return $this->redirectToRoute('app_admin_forum');
        }

        $this->denyUnlessCanManage($thread, $threadService, $currentUserService);

        $allowedStatuses = [
            ThreadStatus::OPEN,
            ThreadStatus::LOCKED,
            ThreadStatus::ARCHIVED,
        ];

        $targetStatus = ThreadStatus::tryFrom($status);
        if ($targetStatus === null || !in_array($targetStatus, $allowedStatuses, true)) {
            throw $this->createNotFoundException('Invalid thread status action.');
        }

        $threadService->updateStatus($thread, $targetStatus);

        return $this->redirectToRoute('app_forum_thread_detail', ['id' => $thread->getId()]);
    }

    #[Route('/{id}/pin', name: 'app_thread_pin')]
    public function pin(ForumThread $thread, ThreadService $threadService, CurrentUserService $currentUserService): Response
    {
        if ($currentUserService->isAdmin($currentUserService->requireUser())) {
            return $this->redirectToRoute('app_admin_forum');
        }

        $this->denyUnlessCanManage($thread, $threadService, $currentUserService);
        $threadService->togglePin($thread);

        return $this->redirectToRoute('app_forum_feed');
    }

    #[Route('/{id}/upvote', name: 'app_thread_upvote')]
    public function upvote(ForumThread $thread, InteractionService $interactionService, CurrentUserService $currentUserService): Response
    {
        $currentUser = $currentUserService->requireUser();
        if ($currentUserService->isAdmin($currentUser)) {
            return $this->redirectToRoute('app_admin_forum');
        }

        $interactionService->toggleUpvote($thread, $currentUser);

        return $this->redirectToRoute('app_forum_thread_detail', ['id' => $thread->getId()]);
    }

    #[Route('/{id}/downvote', name: 'app_thread_downvote')]
    public function downvote(ForumThread $thread, InteractionService $interactionService, CurrentUserService $currentUserService): Response
    {
        $currentUser = $currentUserService->requireUser();
        if ($currentUserService->isAdmin($currentUser)) {
            return $this->redirectToRoute('app_admin_forum');
        }

        $interactionService->toggleDownvote($thread, $currentUser);

        return $this->redirectToRoute('app_forum_thread_detail', ['id' => $thread->getId()]);
    }

    #[Route('/{id}/follow', name: 'app_thread_follow')]
    public function follow(ForumThread $thread, InteractionService $interactionService, CurrentUserService $currentUserService): Response
    {
        $currentUser = $currentUserService->requireUser();
        if ($currentUserService->isAdmin($currentUser)) {
            return $this->redirectToRoute('app_admin_forum');
        }

        $interactionService->toggleFollow($thread, $currentUser);

        return $this->redirectToRoute('app_forum_thread_detail', ['id' => $thread->getId()]);
    }

    private function denyUnlessCanManage(ForumThread $thread, ThreadService $threadService, CurrentUserService $currentUserService): void
    {
        if (!$threadService->canEdit($thread, $currentUserService->requireUser()->getId())) {
            throw $this->createAccessDeniedException('You cannot modify this thread.');
        }
    }
}
