<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class CategoryServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;
    private CategoryService $categoryService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->slugger = $this->createMock(SluggerInterface::class);
        $this->categoryService = new CategoryService($this->entityManager, $this->slugger);
    }

    // --- save() tests ---

    /**
     * When the category has no slug yet, the slugger should be called
     * and the generated slug assigned before persisting.
     */
    public function testSaveGeneratesSlugWhenMissing(): void
    {
        $category = new Category();
        $category->setName('My Category');
        // No slug set → slug is null/empty

        $slugResult = $this->createMock(UnicodeString::class);
        $slugResult->method('lower')->willReturn(new UnicodeString('my-category'));

        $this->slugger
            ->expects($this->once())
            ->method('slug')
            ->with('My Category')
            ->willReturn($slugResult);

        $this->entityManager->expects($this->once())->method('persist')->with($category);
        $this->entityManager->expects($this->once())->method('flush');

        $this->categoryService->save($category);

        $this->assertSame('my-category', $category->getSlug());
    }

    /**
     * When the category already has a slug, the slugger must NOT be called
     * and the existing slug must be preserved.
     */
    public function testSaveDoesNotOverwriteExistingSlug(): void
    {
        $category = new Category();
        $category->setName('My Category');
        $category->setSlug('custom-slug');

        $this->slugger->expects($this->never())->method('slug');

        $this->entityManager->expects($this->once())->method('persist')->with($category);
        $this->entityManager->expects($this->once())->method('flush');

        $this->categoryService->save($category);

        $this->assertSame('custom-slug', $category->getSlug());
    }

    /**
     * save() must always call persist() and flush(), regardless of slug state.
     */
    public function testSavePersistsAndFlushes(): void
    {
        $category = new Category();
        $category->setName('Tech');
        $category->setSlug('tech');

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->categoryService->save($category);
    }

    // --- delete() tests ---

    /**
     * delete() must call remove() and flush() on the entity manager.
     */
    public function testDeleteRemovesAndFlushes(): void
    {
        $category = new Category();
        $category->setName('To Delete');
        $category->setSlug('to-delete');

        $this->entityManager->expects($this->once())->method('remove')->with($category);
        $this->entityManager->expects($this->once())->method('flush');

        $this->categoryService->delete($category);
    }
}
