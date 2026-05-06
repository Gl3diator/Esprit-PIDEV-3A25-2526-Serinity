<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\ThreadManageController;
use App\Entity\ForumThread;
use App\Service\AuthenticationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Unit tests for the pure-logic private helpers of ThreadManageController.
 *
 * Route-level behaviour (new thread, edit, delete, etc.) requires a full
 * WebTestCase boot because those actions depend on forms, CSRF tokens,
 * security voters, and the DI container.  Here we focus on the small,
 * side-effect-free helpers that are directly testable in isolation.
 */
class ThreadManageControllerTest extends TestCase
{
    private ThreadManageController $controller;
    private TokenStorageInterface $tokenStorage;
    private AuthenticationService $authenticationService;

    protected function setUp(): void
    {
        $this->tokenStorage         = $this->createMock(TokenStorageInterface::class);
        $this->authenticationService = $this->createMock(AuthenticationService::class);

        // Expose private helpers via an anonymous subclass
        $this->controller = new class(
            $this->tokenStorage,
            $this->authenticationService,
        ) extends ThreadManageController {
            public function publicExpectsJson(Request $request): bool
            {
                return $this->expectsJson($request);
            }

            public function publicReplyPrefillKey(int $threadId): string
            {
                return $this->replyPrefillKey($threadId);
            }

            public function publicStoreReplyPrefill(Request $request, ForumThread $thread, string $content): void
            {
                $this->storeReplyPrefill($request, $thread, $content);
            }
        };
    }

    // =========================================================================
    // expectsJson()
    // =========================================================================

    public function testExpectsJsonReturnsFalseForRegularHtmlRequest(): void
    {
        $request = new Request();
        $this->assertFalse($this->controller->publicExpectsJson($request));
    }

    public function testExpectsJsonReturnsTrueForXmlHttpRequest(): void
    {
        $request = new Request();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $this->assertTrue($this->controller->publicExpectsJson($request));
    }

    public function testExpectsJsonReturnsTrueWhenAcceptHeaderIsJson(): void
    {
        $request = new Request();
        $request->headers->set('Accept', 'application/json');

        $this->assertTrue($this->controller->publicExpectsJson($request));
    }

    public function testExpectsJsonReturnsTrueWhenAcceptHeaderContainsJson(): void
    {
        $request = new Request();
        $request->headers->set('Accept', 'text/html, application/json, */*');

        $this->assertTrue($this->controller->publicExpectsJson($request));
    }

    public function testExpectsJsonReturnsFalseForHtmlOnlyAcceptHeader(): void
    {
        $request = new Request();
        $request->headers->set('Accept', 'text/html, */*');

        $this->assertFalse($this->controller->publicExpectsJson($request));
    }

    // =========================================================================
    // replyPrefillKey()
    // =========================================================================

    public function testReplyPrefillKeyContainsThreadId(): void
    {
        $key = $this->controller->publicReplyPrefillKey(42);
        $this->assertStringContainsString('42', $key);
    }

    public function testReplyPrefillKeyIsConsistentForSameId(): void
    {
        $this->assertSame(
            $this->controller->publicReplyPrefillKey(7),
            $this->controller->publicReplyPrefillKey(7),
        );
    }

    public function testReplyPrefillKeyDiffersForDifferentIds(): void
    {
        $this->assertNotSame(
            $this->controller->publicReplyPrefillKey(1),
            $this->controller->publicReplyPrefillKey(2),
        );
    }

    // =========================================================================
    // storeReplyPrefill()
    // =========================================================================

    private function makeRequestWithSession(): Request
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        return $request;
    }

    private function makeThread(int $id): ForumThread
    {
        $thread = $this->createMock(ForumThread::class);
        $thread->method('getId')->willReturn($id);

        return $thread;
    }

    public function testStoreReplyPrefillSavesContentInSession(): void
    {
        $request = $this->makeRequestWithSession();
        $thread  = $this->makeThread(99);

        $this->controller->publicStoreReplyPrefill($request, $thread, 'My draft content');

        $key   = $this->controller->publicReplyPrefillKey(99);
        $value = $request->getSession()->get($key);

        $this->assertSame('My draft content', $value);
    }

    public function testStoreReplyPrefillDoesNothingForEmptyContent(): void
    {
        $request = $this->makeRequestWithSession();
        $thread  = $this->makeThread(99);

        $this->controller->publicStoreReplyPrefill($request, $thread, '');

        $key = $this->controller->publicReplyPrefillKey(99);
        $this->assertNull($request->getSession()->get($key));
    }

    public function testStoreReplyPrefillDoesNothingForWhitespaceOnlyContent(): void
    {
        $request = $this->makeRequestWithSession();
        $thread  = $this->makeThread(99);

        $this->controller->publicStoreReplyPrefill($request, $thread, '   ');

        $key = $this->controller->publicReplyPrefillKey(99);
        $this->assertNull($request->getSession()->get($key));
    }

    public function testStoreReplyPrefillTruncatesLongContent(): void
    {
        $request = $this->makeRequestWithSession();
        $thread  = $this->makeThread(1);

        // Content longer than the 5 000-character cap
        $longContent = str_repeat('a', 6000);
        $this->controller->publicStoreReplyPrefill($request, $thread, $longContent);

        $key   = $this->controller->publicReplyPrefillKey(1);
        $value = (string) $request->getSession()->get($key);

        $this->assertLessThanOrEqual(5000, mb_strlen($value));
    }

    public function testStoreReplyPrefillDoesNothingWhenRequestHasNoSession(): void
    {
        // A plain Request has no session — the method should bail out silently
        $request = new Request(); // no session attached
        $thread  = $this->makeThread(5);

        // Must not throw
        $this->controller->publicStoreReplyPrefill($request, $thread, 'some content');

        $this->assertTrue(true); // reached here without exception
    }

    public function testStoreReplyPrefillStripsLeadingAndTrailingWhitespace(): void
    {
        $request = $this->makeRequestWithSession();
        $thread  = $this->makeThread(10);

        $this->controller->publicStoreReplyPrefill($request, $thread, '  hello world  ');

        $key   = $this->controller->publicReplyPrefillKey(10);
        $value = $request->getSession()->get($key);

        // trim() is applied before storing, so stored value should be trimmed
        $this->assertSame('hello world', $value);
    }
}
