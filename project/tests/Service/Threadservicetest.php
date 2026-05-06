<?php

namespace App\Tests\Service;

use App\Entity\ForumThread;
use App\Enum\ThreadStatus;
use App\Repository\ForumThreadRepository;
use App\Service\ModerationService;
use App\Service\ThreadService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ThreadServiceTest extends TestCase
{
    private ForumThreadRepository&MockObject $threadRepository;
    private EntityManagerInterface&MockObject $entityManager;
    private ModerationService&MockObject $moderationService;
    private ThreadService $threadService;

    protected function setUp(): void
    {
        $this->threadRepository   = $this->createMock(ForumThreadRepository::class);
        $this->entityManager      = $this->createMock(EntityManagerInterface::class);
        $this->moderationService  = $this->createMock(ModerationService::class);

        $this->threadService = new ThreadService(
            $this->threadRepository,
            $this->entityManager,
            $this->moderationService,
        );
    }

    // -------------------------------------------------------------------------
    // feed()
    // -------------------------------------------------------------------------

    public function testFeedDelegatestoRepository(): void
    {
        $filters = ['status' => 'open'];
        $threads = [$this->createMock(ForumThread::class)];

        $this->threadRepository
            ->expects($this->once())
            ->method('findFeed')
            ->with($filters)
            ->willReturn($threads);

        $result = $this->threadService->feed($filters);

        $this->assertSame($threads, $result);
    }

    public function testFeedPassesEmptyFiltersByDefault(): void
    {
        $this->threadRepository
            ->expects($this->once())
            ->method('findFeed')
            ->with([])
            ->willReturn([]);

        $this->threadService->feed();
    }

    // -------------------------------------------------------------------------
    // saveThread()
    // -------------------------------------------------------------------------

    public function testSaveThreadPersistsAndFlushesCleanThread(): void
    {
        $thread = $this->makeThread(title: 'Hello World', content: 'Nice post.');

        $this->moderationService->method('isToxic')->willReturn(false);

        $this->entityManager->expects($this->once())->method('persist')->with($thread);
        $this->entityManager->expects($this->once())->method('flush');

        $this->threadService->saveThread($thread);
    }

    public function testSaveThreadSetsUpdatedAt(): void
    {
        $thread = $this->makeThread(title: 'Hi', content: 'Content');

        $this->moderationService->method('isToxic')->willReturn(false);
        $this->entityManager->method('persist');
        $this->entityManager->method('flush');

        $before = new \DateTimeImmutable();
        $this->threadService->saveThread($thread);

        $this->assertInstanceOf(\DateTimeImmutable::class, $thread->getUpdatedAt());
        $this->assertGreaterThanOrEqual($before, $thread->getUpdatedAt());
    }

    public function testSaveThreadThrowsWhenTitleIsToxic(): void
    {
        $thread = $this->makeThread(title: 'toxic title', content: 'fine content');

        $this->moderationService
            ->method('isToxic')
            ->willReturnMap([
                ['toxic title', true],
                ['fine content', false],
            ]);

        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Thread contains inappropriate content and cannot be published.');

        $this->threadService->saveThread($thread);
    }

    public function testSaveThreadThrowsWhenContentIsToxic(): void
    {
        $thread = $this->makeThread(title: 'fine title', content: 'toxic content');

        $this->moderationService
            ->method('isToxic')
            ->willReturnMap([
                ['fine title', false],
                ['toxic content', true],
            ]);

        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        $this->expectException(\RuntimeException::class);

        $this->threadService->saveThread($thread);
    }

    public function testSaveThreadHandlesNullTitleAndContent(): void
    {
        $thread = $this->makeThread(title: null, content: null);

        $this->moderationService
            ->expects($this->exactly(2))
            ->method('isToxic')
            ->willReturnMap([
                ['', false],
                ['', false],
            ]);

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->threadService->saveThread($thread);
    }

    // -------------------------------------------------------------------------
    // deleteThread()
    // -------------------------------------------------------------------------

    public function testDeleteThreadRemovesAndFlushes(): void
    {
        $thread = $this->createMock(ForumThread::class);

        $this->entityManager->expects($this->once())->method('remove')->with($thread);
        $this->entityManager->expects($this->once())->method('flush');

        $this->threadService->deleteThread($thread);
    }

    public function testDeleteThreadNeverCallsPersist(): void
    {
        $thread = $this->createMock(ForumThread::class);

        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->method('remove');
        $this->entityManager->method('flush');

        $this->threadService->deleteThread($thread);
    }

    // -------------------------------------------------------------------------
    // updateStatus()
    // -------------------------------------------------------------------------

    public function testUpdateStatusSetsStatusAndUpdatedAt(): void
    {
        $thread = $this->makeThread();

        $this->entityManager->expects($this->once())->method('flush');

        $before = new \DateTimeImmutable();
        $this->threadService->updateStatus($thread, ThreadStatus::Closed);

        $this->assertSame(ThreadStatus::Closed, $thread->getStatus());
        $this->assertGreaterThanOrEqual($before, $thread->getUpdatedAt());
    }

    public function testUpdateStatusDoesNotCallPersist(): void
    {
        $thread = $this->makeThread();

        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->method('flush');

        $this->threadService->updateStatus($thread, ThreadStatus::Open);
    }

    // -------------------------------------------------------------------------
    // togglePin()
    // -------------------------------------------------------------------------

    public function testTogglePinPinsUnpinnedThread(): void
    {
        $thread = $this->makeThread(isPinned: false);

        $this->entityManager->expects($this->once())->method('flush');

        $this->threadService->togglePin($thread);

        $this->assertTrue($thread->isPinned());
    }

    public function testTogglePinUnpinsPinnedThread(): void
    {
        $thread = $this->makeThread(isPinned: true);

        $this->entityManager->expects($this->once())->method('flush');

        $this->threadService->togglePin($thread);

        $this->assertFalse($thread->isPinned());
    }

    public function testTogglePinDoesNotCallPersist(): void
    {
        $thread = $this->makeThread(isPinned: false);

        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->method('flush');

        $this->threadService->togglePin($thread);
    }

    // -------------------------------------------------------------------------
    // canEdit()
    // -------------------------------------------------------------------------

    /** @dataProvider canEditProvider */
    public function testCanEdit(string $authorId, ?string $userId, bool $expected): void
    {
        $thread = $this->makeThread(authorId: $authorId);

        $this->assertSame($expected, $this->threadService->canEdit($thread, $userId));
    }

    /** @return array<string, array{string, ?string, bool}> */
    public static function canEditProvider(): array
    {
        return [
            'matching user can edit'          => ['user-123', 'user-123', true],
            'different user cannot edit'      => ['user-123', 'user-456', false],
            'null userId cannot edit'         => ['user-123', null,       false],
            'empty string userId cannot edit' => ['user-123', '',         false],
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeThread(
        ?string $title    = 'Default Title',
        ?string $content  = 'Default content.',
        string  $authorId = 'user-1',
        bool    $isPinned = false,
    ): ForumThread {
        $thread = new ForumThread();
        $thread->setTitle($title);
        $thread->setContent($content);
        $thread->setAuthorId($authorId);
        $thread->setIsPinned($isPinned);

        return $thread;
    }
}