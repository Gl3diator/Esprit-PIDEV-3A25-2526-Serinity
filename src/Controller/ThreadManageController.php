<?php

namespace App\Controller;

use App\Entity\ForumThread;
use App\Enum\ThreadStatus;
use App\Form\ForumThreadType;
use App\Service\CurrentUserService;
use App\Service\ForumRateLimitService;
use App\Service\ImageUploadService;
use App\Service\InteractionService;
use App\Service\ThreadDuplicateRadarService;
use App\Service\ThreadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/threads')]
class ThreadManageController extends AbstractController
{
    #[Route('/new', name: 'app_thread_new')]
    public function new(
        Request $request,
        ThreadService $threadService,
        CurrentUserService $currentUserService,
        ImageUploadService $uploadService,
        ThreadDuplicateRadarService $threadDuplicateRadarService,
    ): Response
    {
        $currentUser = $currentUserService->requireUser();
        if ($currentUserService->isAdmin($currentUser)) {
            return $this->redirectToRoute('app_admin_forum');
        }

        $threadError = null;
        $duplicateRadarResults = [];
        $thread = new ForumThread();
        $form = $this->createForm(ForumThreadType::class, $thread);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forcePublish = $request->request->getBoolean('force_publish');

            if (!$forcePublish) {
                $duplicateRadarResults = $threadDuplicateRadarService->findNearDuplicates(
                    $currentUser->getId(),
                    (string) $thread->getTitle(),
                    (string) $thread->getContent(),
                );

                if ($duplicateRadarResults !== []) {
                    $this->addFlash('warning', 'Possible duplicate threads detected. Choose Continue, Merge, or Revive.');

                    return $this->render('thread/form.html.twig', [
                        'form' => $form,
                        'mode' => 'create',
                        'currentUser' => $currentUser,
                        'threadError' => $threadError,
                        'duplicateRadarResults' => $duplicateRadarResults,
                    ]);
                }
            }

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
            'duplicateRadarResults' => $duplicateRadarResults,
        ]);
    }

    #[Route('/new/duplicate-radar', name: 'app_thread_duplicate_radar', methods: ['POST'])]
    public function duplicateRadar(Request $request, CurrentUserService $currentUserService, ThreadDuplicateRadarService $threadDuplicateRadarService): Response
    {
        $currentUser = $currentUserService->requireUser();
        if ($currentUserService->isAdmin($currentUser)) {
            return $this->json([
                'ok' => false,
                'message' => 'Admins do not use this endpoint.',
            ], Response::HTTP_FORBIDDEN);
        }

        if (!$this->isCsrfTokenValid('duplicate_radar', (string) $request->request->get('_token'))) {
            return $this->json([
                'ok' => false,
                'message' => 'Invalid duplicate radar token.',
            ], Response::HTTP_FORBIDDEN);
        }

        $title = (string) $request->request->get('title', '');
        $content = (string) $request->request->get('content', '');

        $duplicates = $threadDuplicateRadarService->findNearDuplicates($currentUser->getId(), $title, $content);

        return $this->json([
            'ok' => true,
            'duplicates' => $duplicates,
        ]);
    }

    #[Route('/new/merge/{id}', name: 'app_thread_duplicate_merge', methods: ['POST'])]
    public function mergeDuplicate(ForumThread $thread, Request $request, CurrentUserService $currentUserService): Response
    {
        $currentUser = $currentUserService->requireUser();
        if ($currentUserService->isAdmin($currentUser)) {
            return $this->redirectToRoute('app_admin_forum');
        }

        if (!$this->isCsrfTokenValid('duplicate_flow', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid duplicate merge token.');
        }

        $this->storeReplyPrefill($request, $thread, (string) $request->request->get('draft_content', ''));
        $this->addFlash('warning', 'Draft moved to reply composer so you can merge into this thread.');

        return $this->redirectToRoute('app_forum_thread_detail', [
            'id' => $thread->getId(),
            'compose' => 1,
        ]);
    }

    #[Route('/new/revive/{id}', name: 'app_thread_duplicate_revive', methods: ['POST'])]
    public function reviveDuplicate(ForumThread $thread, Request $request, CurrentUserService $currentUserService, ThreadService $threadService): Response
    {
        $currentUser = $currentUserService->requireUser();
        if ($currentUserService->isAdmin($currentUser)) {
            return $this->redirectToRoute('app_admin_forum');
        }

        if (!$this->isCsrfTokenValid('duplicate_flow', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid duplicate revive token.');
        }

        if ($thread->getStatus() === ThreadStatus::ARCHIVED) {
            $threadService->updateStatus($thread, ThreadStatus::OPEN);
            $this->addFlash('warning', 'Archived thread revived and reopened.');
        }

        $this->storeReplyPrefill($request, $thread, (string) $request->request->get('draft_content', ''));
        $this->addFlash('warning', 'Draft moved to reply composer so you can revive this thread.');

        return $this->redirectToRoute('app_forum_thread_detail', [
            'id' => $thread->getId(),
            'compose' => 1,
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
    public function follow(
        ForumThread $thread,
        Request $request,
        InteractionService $interactionService,
        CurrentUserService $currentUserService,
        ForumRateLimitService $forumRateLimitService,
    ): Response
    {
        $currentUser = $currentUserService->requireUser();
        if ($currentUserService->isAdmin($currentUser)) {
            return $this->redirectToRoute('app_admin_forum');
        }

        $rateLimit = $forumRateLimitService->consumeFollowToggle($currentUser->getId());
        if (!$rateLimit->isAccepted()) {
            $retryAt = $rateLimit->getRetryAfter();
            $retryAfterSeconds = $retryAt === null ? null : max(1, $retryAt->getTimestamp() - time());
            $message = 'Too many follow/unfollow actions. Please wait a moment before trying again.';

            if ($this->expectsJson($request)) {
                $isFollowing = $interactionService->getInteraction($thread, $currentUser)?->isFollow() ?? false;

                return $this->json([
                    'ok' => false,
                    'limited' => true,
                    'message' => $message,
                    'retryAfterSeconds' => $retryAfterSeconds,
                    'isFollowing' => $isFollowing,
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }

            $this->addFlash('warning', $message);

            return $this->redirectToRoute('app_forum_thread_detail', ['id' => $thread->getId()]);
        }

        $interactionService->toggleFollow($thread, $currentUser);

        $isFollowing = $interactionService->getInteraction($thread, $currentUser)?->isFollow() ?? false;

        if ($this->expectsJson($request)) {
            return $this->json([
                'ok' => true,
                'limited' => false,
                'isFollowing' => $isFollowing,
            ]);
        }

        return $this->redirectToRoute('app_forum_thread_detail', ['id' => $thread->getId()]);
    }

    private function expectsJson(Request $request): bool
    {
        return $request->isXmlHttpRequest() || str_contains((string) $request->headers->get('Accept', ''), 'application/json');
    }

    private function storeReplyPrefill(Request $request, ForumThread $thread, string $content): void
    {
        $text = trim($content);
        if ($text === '' || !$request->hasSession()) {
            return;
        }

        $request->getSession()->set($this->replyPrefillKey((int) $thread->getId()), mb_substr($text, 0, 5000));
    }

    private function replyPrefillKey(int $threadId): string
    {
        return 'thread_reply_prefill_'.$threadId;
    }

    private function denyUnlessCanManage(ForumThread $thread, ThreadService $threadService, CurrentUserService $currentUserService): void
    {
        if (!$threadService->canEdit($thread, $currentUserService->requireUser()->getId())) {
            throw $this->createAccessDeniedException('You cannot modify this thread.');
        }
    }
}
