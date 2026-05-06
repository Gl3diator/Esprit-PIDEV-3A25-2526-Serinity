<?php

namespace App\Tests\Service;

use App\Entity\ForumThread;
use App\Enum\ThreadStatus;
use App\Repository\ForumThreadRepository;
use App\Service\ModerationService;
use App\Service\ThreadService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ThreadServiceTest extends TestCase
{
    private ForumThreadRepository $threadRepository;
    private EntityManagerInterface $entityManager;
    private ModerationService $moderationService;
    private ThreadService $threadService;

    protected function setUp(): void
    {
        $this->threadRepository   = $this->createMock(ForumThreadRepository::class);
        $this->entityManager      = $this->createMock(EntityManagerInterface::class);
        $this->moderationService  = $this->createMock(ModerationService::class);

        $this->threadService = new ThreadService(
            $this->threadRepository,
            $this->entityManager,
            $this->moderationService
        );
    }

    // --- feed() tests ---

    /**
     * feed() with no filters should delegate to the repository and return its result.
     */
    public function testFeedWithNoFiltersReturnsRepositoryResult(): void
    {
        $thread = $this->createMock(ForumThread::class);

        $this->threadRepository
            ->expects($this->once())
            ->method('findFeed')
            ->with([])
            ->willReturn([$thread]);

        $result = $this->threadService->feed();

        $this->assertCount(1, $result);
        $this->assertSame($thread, $result[0]);
    }

    /**
     * feed() should forward any filters it receives to the repository.
     */
    public function testFeedPassesFiltersToRepository(): void
    {
        $filters = ['status' => 'open', 'category' => 42];

        $this->threadRepository
            ->expects($this->once())
            ->method('findFeed')
            ->with($filters)
            ->willReturn([]);

        $this->threadService->feed($filters);
    }

    // --- saveThread() tests ---

    /**
     * A clean thread (no toxic content) should be touched, persisted and flushed.
     */
    public function testSaveThreadPersistsCleanThread(): void
    {
        $thread = $this->createMock(ForumThread::class);
        $thread->method('getTitle')->willReturn('A valid title');
        $thread->method('getContent')->willReturn('Some clean content.');

        $this->moderationService->method('isToxic')->willReturn(false);

        $thread->expects($this->once())->method('touch');
        $this->entityManager->expects($this->once())->method('persist')->with($thread);
        $this->entityManager->expects($this->once())->method('flush');

        $this->threadService->saveThread($thread);
    }

    /**
     * A thread with a toxic title must throw RuntimeException and never be persisted.
     */
    public function testSaveThreadThrowsExceptionForToxicTitle(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Thread contains inappropriate content and cannot be published.');

        $thread = $this->createMock(ForumThread::class);
        $thread->method('getTitle')->willReturn('toxic title here');
        $thread->method('getContent')->willReturn('Clean content.');

        $this->moderationService
            ->method('isToxic')
            ->willReturnCallback(fn(string $text) => str_contains($text, 'toxic'));

        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        $this->threadService->saveThread($thread);
    }

    /**
     * A thread with toxic content must throw RuntimeException and never be persisted.
     */
    public function testSaveThreadThrowsExceptionForToxicContent(): void
    {
        $this->expectException(\RuntimeException::class);

        $thread = $this->createMock(ForumThread::class);
        $thread->method('getTitle')->willReturn('Normal title');
        $thread->method('getContent')->willReturn('toxic content here');

        $this->moderationService
            ->method('isToxic')
            ->willReturnCallback(fn(string $text) => str_contains($text, 'toxic'));

        $this->entityManager->expects($this->never())->method('persist');

        $this->threadService->saveThread($thread);
    }

    // --- deleteThread() tests ---

    /**
     * deleteThread() must call remove() and flush() on the entity manager.
     */
    public function testDeleteThreadRemovesAndFlushes(): void
    {
        $thread = $this->createMock(ForumThread::class);

        $this->entityManager->expects($this->once())->method('remove')->with($thread);
        $this->entityManager->expects($this->once())->method('flush');

        $this->threadService->deleteThread($thread);
    }

    // --- updateStatus() tests ---

    /**
     * updateStatus() must set the new status, touch the thread and flush.
     */
    public function testUpdateStatusSetsStatusAndFlushes(): void
    {
        $thread = $this->createMock(ForumThread::class);
        $status = ThreadStatus::Closed; // adjust to your actual enum case

        $thread->expects($this->once())->method('setStatus')->with($status);
        $thread->expects($this->once())->method('touch');
        $this->entityManager->expects($this->once())->method('flush');

        $this->threadService->updateStatus($thread, $status);
    }

    // --- togglePin() tests ---

    /**
     * togglePin() must invert the pinned state and flush.
     * Scenario: thread is currently pinned → should become unpinned.
     */
    public function testTogglePinUnpinsAPinnedThread(): void
    {
        $thread = $this->createMock(ForumThread::class);
        $thread->method('isPinned')->willReturn(true);

        $thread->expects($this->once())->method('setIsPinned')->with(false);
        $this->entityManager->expects($this->once())->method('flush');

        $this->threadService->togglePin($thread);
    }

    /**
     * togglePin() – scenario: thread is not pinned → should become pinned.
     */
    public function testTogglePinPinsAnUnpinnedThread(): void
    {
        $thread = $this->createMock(ForumThread::class);
        $thread->method('isPinned')->willReturn(false);

        $thread->expects($this->once())->method('setIsPinned')->with(true);
        $this->entityManager->expects($this->once())->method('flush');

        $this->threadService->togglePin($thread);
    }

    // --- canEdit() tests ---

    /**
     * canEdit() must return true when the userId matches the thread's author.
     */
    public function testCanEditReturnsTrueForAuthor(): void
    {
        $thread = $this->createMock(ForumThread::class);
        $thread->method('getAuthorId')->willReturn('user-42');

        $this->assertTrue($this->threadService->canEdit($thread, 'user-42'));
    }

    /**
     * canEdit() must return false when the userId does not match the author.
     */
    public function testCanEditReturnsFalseForDifferentUser(): void
    {
        $thread = $this->createMock(ForumThread::class);
        $thread->method('getAuthorId')->willReturn('user-42');

        $this->assertFalse($this->threadService->canEdit($thread, 'user-99'));
    }

    /**
     * canEdit() must return false when userId is null.
     */
    public function testCanEditReturnsFalseForNullUser(): void
    {
        $thread = $this->createMock(ForumThread::class);

        $this->assertFalse($this->threadService->canEdit($thread, null));
    }

    /**
     * canEdit() must return false when userId is an empty string.
     */
    public function testCanEditReturnsFalseForEmptyStringUser(): void
    {
        $thread = $this->createMock(ForumThread::class);

        $this->assertFalse($this->threadService->canEdit($thread, ''));
    }
}
