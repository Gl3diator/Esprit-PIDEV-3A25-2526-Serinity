<?php

namespace App\Tests\Service;

use App\Entity\Category;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class CategoryServiceTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private SluggerInterface&MockObject $slugger;
    private CategoryService $categoryService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->slugger = $this->createMock(SluggerInterface::class);

        $this->categoryService = new CategoryService(
            $this->entityManager,
            $this->slugger,
        );
    }

    // --- save() ---

    public function testSaveGeneratesSlugWhenSlugIsEmpty(): void
    {
        $category = new Category();
        $category->setName('My Cool Category');

        $slugString = new UnicodeString('my-cool-category');

        $this->slugger
            ->expects($this->once())
            ->method('slug')
            ->with('My Cool Category')
            ->willReturn($slugString);

        $this->entityManager->expects($this->once())->method('persist')->with($category);
        $this->entityManager->expects($this->once())->method('flush');

        $this->categoryService->save($category);

        $this->assertSame('my-cool-category', $category->getSlug());
    }

    public function testSaveDoesNotOverrideExistingSlug(): void
    {
        $category = new Category();
        $category->setName('My Cool Category');
        $category->setSlug('custom-slug');

        $this->slugger->expects($this->never())->method('slug');

        $this->entityManager->expects($this->once())->method('persist')->with($category);
        $this->entityManager->expects($this->once())->method('flush');

        $this->categoryService->save($category);

        $this->assertSame('custom-slug', $category->getSlug());
    }

    public function testSavePersistsAndFlushes(): void
    {
        $category = new Category();
        $category->setName('Test');
        $category->setSlug('test');

        $this->entityManager->expects($this->once())->method('persist')->with($category);
        $this->entityManager->expects($this->once())->method('flush');

        $this->categoryService->save($category);
    }

    public function testSaveSlugIsLowercased(): void
    {
        $category = new Category();
        $category->setName('UPPER CASE NAME');

        // slug() returns a UnicodeString; lower() on it returns the lowercased version
        $slugString = new UnicodeString('upper-case-name');

        $this->slugger
            ->method('slug')
            ->willReturn($slugString);

        $this->entityManager->method('persist');
        $this->entityManager->method('flush');

        $this->categoryService->save($category);

        $this->assertSame('upper-case-name', $category->getSlug());
    }

    // --- delete() ---

    public function testDeleteRemovesAndFlushes(): void
    {
        $category = new Category();
        $category->setName('To Delete');

        $this->entityManager->expects($this->once())->method('remove')->with($category);
        $this->entityManager->expects($this->once())->method('flush');

        $this->categoryService->delete($category);
    }

    public function testDeleteDoesNotCallPersist(): void
    {
        $category = new Category();

        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush');

        $this->categoryService->delete($category);
    }
}