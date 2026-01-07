<?php

namespace ProcessWire\Tests;

use PHPUnit\Framework\TestCase;
use ProcessWire\Article;
use ProcessWire\Page;
use ProcessWire\MockPages;
use ProcessWire\ApiNotFoundException;
use ProcessWire\ApiValidationException;

class ArticleTest extends TestCase {

    protected function setUp(): void {
        MockPages::clearMockPages();
    }

    protected function createMockArticle(int $id, string $name): Page {
        $article = new Page();
        $article->id = $id;
        $article->name = $name;
        $article->title = ucfirst($name);
        $article->template->name = 'article';
        $article->ingress = 'Test ingress';
        $article->body = 'Test body content';

        MockPages::addMockPage($article);
        return $article;
    }

    public function testGetArticlesReturnsEmptyArrayWhenNoArticles(): void {
        $result = Article::getArticles();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('articles', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertEmpty($result['articles']);
    }

    public function testGetArticlesReturnsPaginationMeta(): void {
        $this->createMockArticle(1, 'article-1');
        $this->createMockArticle(2, 'article-2');

        $result = Article::getArticles();

        $this->assertEquals(2, $result['pagination']['total']);
        $this->assertEquals(50, $result['pagination']['limit']);
        $this->assertEquals(0, $result['pagination']['offset']);
        $this->assertFalse($result['pagination']['hasMore']);
    }

    public function testGetArticlesRespectsPagination(): void {
        for ($i = 1; $i <= 5; $i++) {
            $this->createMockArticle($i, "article-{$i}");
        }

        $data = (object) ['limit' => 2, 'offset' => 0];
        $result = Article::getArticles($data);

        $this->assertCount(2, $result['articles']);
        $this->assertEquals(5, $result['pagination']['total']);
        $this->assertTrue($result['pagination']['hasMore']);
    }

    public function testGetArticleReturnsArticle(): void {
        $this->createMockArticle(123, 'test-article');

        $data = (object) ['id' => 123];
        $result = Article::getArticle($data);

        $this->assertEquals(123, $result['id']);
        $this->assertEquals('test-article', $result['name']);
        $this->assertEquals('Test-article', $result['title']);
    }

    public function testGetArticleThrowsNotFoundForMissingArticle(): void {
        $this->expectException(ApiNotFoundException::class);
        $this->expectExceptionMessage('Article not found');

        $data = (object) ['id' => 999];
        Article::getArticle($data);
    }

    public function testGetArticleThrowsApiValidationExceptionForWrongTemplate(): void {
        $page = new Page();
        $page->id = 100;
        $page->template->name = 'character';
        MockPages::addMockPage($page);

        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage('Page is not an article');

        $data = (object) ['id' => 100];
        Article::getArticle($data);
    }

    public function testCreateArticleRequiresName(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage('Missing required fields: name');

        $data = (object) ['title' => 'Test'];
        Article::createArticle($data);
    }

    public function testCreateArticleValidatesNameLength(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'name' must be at most 100 characters");

        $data = (object) ['name' => str_repeat('a', 101)];
        Article::createArticle($data);
    }

    public function testCreateArticleValidatesTitleLength(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'title' must be at most 200 characters");

        $data = (object) [
            'name' => 'test',
            'title' => str_repeat('a', 201)
        ];
        Article::createArticle($data);
    }

    public function testDeleteArticleThrowsNotFoundForMissingArticle(): void {
        $this->expectException(ApiNotFoundException::class);

        $data = (object) ['id' => 999];
        Article::deleteArticle($data);
    }

    public function testDeleteArticleReturnsSuccessResponse(): void {
        $this->createMockArticle(456, 'old-news');

        $data = (object) ['id' => 456];
        $result = Article::deleteArticle($data);

        $this->assertTrue($result['success']);
        $this->assertEquals(456, $result['id']);
        $this->assertStringContainsString('old-news', $result['message']);
    }

    public function testFormatArticleResponseIncludesAllFields(): void {
        $article = $this->createMockArticle(1, 'test');
        $article->ingress = 'Article summary';
        $article->body = 'Full article body';

        $data = (object) ['id' => 1];
        $result = Article::getArticle($data);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('ingress', $result);
        $this->assertArrayHasKey('body', $result);
        $this->assertArrayHasKey('images', $result);
    }
}
