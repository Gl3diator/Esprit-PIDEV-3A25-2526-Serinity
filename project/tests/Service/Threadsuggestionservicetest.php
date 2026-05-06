<?php

namespace App\Tests\Service;

use App\Entity\ForumThread;
use App\Repository\ForumThreadRepository;
use App\Repository\PostInteractionRepository;
use App\Service\ThreadSuggestionService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ThreadSuggestionServiceTest extends TestCase
{
    private ForumThreadRepository&MockObject $threadRepository;
    private PostInteractionRepository&MockObject $interactionRepository;
    private ThreadSuggestionService $service;

    protected function setUp(): void
    {
        $this->threadRepository      = $this->createMock(ForumThreadRepository::class);
        $this->interactionRepository = $this->createMock(PostInteractionRepository::class);

        $this->service = new ThreadSuggestionService(
            $this->threadRepository,
            $this->interactionRepository,
        );
    }

    // -------------------------------------------------------------------------
    // Returns a category-matched thread on the first positive-score category hit
    // -------------------------------------------------------------------------

    public function testReturnsCategoryThreadWhenFirstCategoryHasMatch(): void
    {
        $userId  = 'user-1';
        $thread  = $this->createMock(ForumThread::class);
        $scores  = [
            ['categoryId' => 10, 'score' => 5],
        ];

        $this->interactionRepository
            ->expects($this->once())
            ->method('findCategoryScoresForUser')
            ->with($userId)
            ->willReturn($scores);

        $this->threadRepository
            ->expects($this->once())
            ->method('findOneSuggestedInCategory')
            ->with($userId, 10)
            ->willReturn($thread);

        $this->threadRepository
            ->expects($this->never())
            ->method('findOneSuggestedAnyCategory');

        $result = $this->service->buildSuggestion($userId);

        $this->assertSame($thread, $result['thread']);
        $this->assertSame($scores, $result['categoryScores']);
    }

    public function testReturnsCategoryThreadFromSecondCategoryWhenFirstHasNoMatch(): void
    {
        $userId        = 'user-1';
        $matchedThread = $this->createMock(ForumThread::class);
        $scores        = [
            ['categoryId' => 10, 'score' => 3],
            ['categoryId' => 20, 'score' => 7],
        ];

        $this->interactionRepository
            ->method('findCategoryScoresForUser')
            ->willReturn($scores);

        $this->threadRepository
            ->expects($this->exactly(2))
            ->method('findOneSuggestedInCategory')
            ->willReturnMap([
                [$userId, 10, null],
                [$userId, 20, $matchedThread],
            ]);

        $this->threadRepository
            ->expects($this->never())
            ->method('findOneSuggestedAnyCategory');

        $result = $this->service->buildSuggestion($userId);

        $this->assertSame($matchedThread, $result['thread']);
    }

    // -------------------------------------------------------------------------
    // Falls back to any-category when no category yields a match
    // -------------------------------------------------------------------------

    public function testFallsBackToAnyCategoryWhenNoCategoryMatchFound(): void
    {
        $userId      = 'user-1';
        $fallback    = $this->createMock(ForumThread::class);
        $scores      = [
            ['categoryId' => 10, 'score' => 2],
            ['categoryId' => 20, 'score' => 4],
        ];

        $this->interactionRepository
            ->method('findCategoryScoresForUser')
            ->willReturn($scores);

        $this->threadRepository
            ->method('findOneSuggestedInCategory')
            ->willReturn(null);

        $this->threadRepository
            ->expects($this->once())
            ->method('findOneSuggestedAnyCategory')
            ->with($userId)
            ->willReturn($fallback);

        $result = $this->service->buildSuggestion($userId);

        $this->assertSame($fallback, $result['thread']);
        $this->assertSame($scores, $result['categoryScores']);
    }

    public function testFallsBackToAnyCategoryReturningNullWhenNoThreadExists(): void
    {
        $userId = 'user-1';
        $scores = [['categoryId' => 10, 'score' => 1]];

        $this->interactionRepository
            ->method('findCategoryScoresForUser')
            ->willReturn($scores);

        $this->threadRepository
            ->method('findOneSuggestedInCategory')
            ->willReturn(null);

        $this->threadRepository
            ->method('findOneSuggestedAnyCategory')
            ->willReturn(null);

        $result = $this->service->buildSuggestion($userId);

        $this->assertNull($result['thread']);
    }

    // -------------------------------------------------------------------------
    // Zero / negative scores are skipped entirely
    // -------------------------------------------------------------------------

    public function testSkipsZeroScoreCategories(): void
    {
        $userId   = 'user-1';
        $fallback = $this->createMock(ForumThread::class);
        $scores   = [
            ['categoryId' => 10, 'score' => 0],
            ['categoryId' => 20, 'score' => 0],
        ];

        $this->interactionRepository
            ->method('findCategoryScoresForUser')
            ->willReturn($scores);

        $this->threadRepository
            ->expects($this->never())
            ->method('findOneSuggestedInCategory');

        $this->threadRepository
            ->expects($this->once())
            ->method('findOneSuggestedAnyCategory')
            ->willReturn($fallback);

        $result = $this->service->buildSuggestion($userId);

        $this->assertSame($fallback, $result['thread']);
    }

    public function testSkipsNegativeScoreCategories(): void
    {
        $userId = 'user-1';
        $scores = [
            ['categoryId' => 10, 'score' => -3],
            ['categoryId' => 20, 'score' => -1],
        ];

        $this->interactionRepository
            ->method('findCategoryScoresForUser')
            ->willReturn($scores);

        $this->threadRepository
            ->expects($this->never())
            ->method('findOneSuggestedInCategory');

        $this->threadRepository
            ->method('findOneSuggestedAnyCategory')
            ->willReturn(null);

        $result = $this->service->buildSuggestion($userId);

        $this->assertNull($result['thread']);
    }

    public function testMissingScoreKeyTreatedAsZeroAndSkipped(): void
    {
        $userId = 'user-1';
        // Row has no 'score' key — the ?? 0 guard should treat it as 0
        $scores = [['categoryId' => 10]];

        $this->interactionRepository
            ->method('findCategoryScoresForUser')
            ->willReturn($scores);

        $this->threadRepository
            ->expects($this->never())
            ->method('findOneSuggestedInCategory');

        $this->threadRepository
            ->method('findOneSuggestedAnyCategory')
            ->willReturn(null);

        $result = $this->service->buildSuggestion($userId);

        $this->assertNull($result['thread']);
    }

    // -------------------------------------------------------------------------
    // Empty score list
    // -------------------------------------------------------------------------

    public function testEmptyCategoryScoresFallsBackToAnyCategory(): void
    {
        $userId   = 'user-1';
        $fallback = $this->createMock(ForumThread::class);

        $this->interactionRepository
            ->method('findCategoryScoresForUser')
            ->willReturn([]);

        $this->threadRepository
            ->expects($this->never())
            ->method('findOneSuggestedInCategory');

        $this->threadRepository
            ->expects($this->once())
            ->method('findOneSuggestedAnyCategory')
            ->willReturn($fallback);

        $result = $this->service->buildSuggestion($userId);

        $this->assertSame($fallback, $result['thread']);
        $this->assertSame([], $result['categoryScores']);
    }

    // -------------------------------------------------------------------------
    // categoryScores are always forwarded unchanged in the return value
    // -------------------------------------------------------------------------

    public function testCategoryScoresAlwaysForwardedOnCategoryMatch(): void
    {
        $userId  = 'user-1';
        $thread  = $this->createMock(ForumThread::class);
        $scores  = [
            ['categoryId' => 5, 'score' => 10],
            ['categoryId' => 6, 'score' => 2],
        ];

        $this->interactionRepository->method('findCategoryScoresForUser')->willReturn($scores);
        $this->threadRepository->method('findOneSuggestedInCategory')->willReturn($thread);

        $result = $this->service->buildSuggestion($userId);

        $this->assertSame($scores, $result['categoryScores']);
    }

    public function testCategoryScoresAlwaysForwardedOnFallback(): void
    {
        $userId  = 'user-1';
        $scores  = [['categoryId' => 5, 'score' => 1]];

        $this->interactionRepository->method('findCategoryScoresForUser')->willReturn($scores);
        $this->threadRepository->method('findOneSuggestedInCategory')->willReturn(null);
        $this->threadRepository->method('findOneSuggestedAnyCategory')->willReturn(null);

        $result = $this->service->buildSuggestion($userId);

        $this->assertSame($scores, $result['categoryScores']);
    }

    // -------------------------------------------------------------------------
    // categoryId is cast to int before being passed to the repository
    // -------------------------------------------------------------------------

    public function testCategoryIdIsCastToInt(): void
    {
        $userId = 'user-1';
        $scores = [['categoryId' => '42', 'score' => 5]]; // string from DB

        $this->interactionRepository
            ->method('findCategoryScoresForUser')
            ->willReturn($scores);

        $this->threadRepository
            ->expects($this->once())
            ->method('findOneSuggestedInCategory')
            ->with($userId, 42) // must be int
            ->willReturn(null);

        $this->threadRepository->method('findOneSuggestedAnyCategory')->willReturn(null);

        $this->service->buildSuggestion($userId);
    }
}