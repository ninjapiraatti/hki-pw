<?php

namespace ProcessWire;

require_once __DIR__ . '/ApiException.php';
require_once __DIR__ . '/Validator.php';

class Article {

    private const TEMPLATE_NAME = 'article';
    private const TEMPLATE_PARENT = 'articles';

    public static function getArticles($data = null) {
        $pagination = Validator::getPaginationParams($data ?? new \stdClass());

        $allArticles = wire('pages')->find("template=" . self::TEMPLATE_NAME);
        $total = $allArticles->count();

        // Apply pagination
        $articles = $allArticles->slice($pagination['offset'], $pagination['limit']);

        $response = [
            'articles' => [],
            'pagination' => Validator::buildPaginationMeta($total, $pagination['limit'], $pagination['offset'])
        ];

        foreach ($articles as $article) {
            $response['articles'][] = self::formatArticleResponse($article);
        }

        return $response;
    }

    public static function getArticle($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

        $article = wire('pages')->get($data->id);

        if (!$article->id) {
            throw new ApiNotFoundException('Article');
        }

        if ($article->template->name !== self::TEMPLATE_NAME) {
            throw new ApiValidationException('Page is not an article');
        }

        return self::formatArticleResponse($article);
    }

    public static function createArticle($data) {
        Validator::validateRequired($data, ['name']);
        Validator::validateString($data->name, 'name', 1, 100);

        if (isset($data->title)) {
            Validator::validateString($data->title, 'title', 0, 200);
        }

        // Create new article page
        $articlesParent = wire('pages')->get('template=' . self::TEMPLATE_PARENT);
        if (!$articlesParent->id) {
            $articlesParent = wire('pages')->get('/');
        }

        $article = new \ProcessWire\Page();
        $article->template = self::TEMPLATE_NAME;
        $article->parent = $articlesParent;
        $article->name = wire('sanitizer')->pageName($data->name);
        $article->title = $data->title ?? $data->name;

        // Set article data
        self::setArticleData($article, $data);

        // Save the article
        $article->save();

        if (!$article->id) {
            throw new ApiServerException('Failed to create article');
        }

        return self::formatArticleResponse($article);
    }

    public static function updateArticle($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

        $article = wire('pages')->get($data->id);

        if (!$article->id) {
            throw new ApiNotFoundException('Article');
        }

        if ($article->template->name !== self::TEMPLATE_NAME) {
            throw new ApiValidationException('Page is not an article');
        }

        // Get JSON data from request body
        $requestBody = Validator::parseJsonBody();

        // Validate input if provided
        if (isset($requestBody->name) && !empty($requestBody->name)) {
            Validator::validateString($requestBody->name, 'name', 1, 100);
        }

        if (isset($requestBody->title)) {
            Validator::validateString($requestBody->title, 'title', 0, 200);
        }

        // Disable output formatting to allow modifications
        $article->of(false);

        // Update name and title if provided
        if (isset($requestBody->name) && !empty($requestBody->name)) {
            $article->name = wire('sanitizer')->pageName($requestBody->name);
        }

        if (isset($requestBody->title)) {
            $article->title = $requestBody->title;
        }

        // Set article data
        self::setArticleData($article, $requestBody);

        // Save the article
        $article->save();

        return self::formatArticleResponse($article);
    }

    public static function deleteArticle($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

        $article = wire('pages')->get($data->id);

        if (!$article->id) {
            throw new ApiNotFoundException('Article');
        }

        if ($article->template->name !== self::TEMPLATE_NAME) {
            throw new ApiValidationException('Page is not an article');
        }

        $id = $article->id;
        $name = $article->name;

        // Delete the article
        wire('pages')->delete($article);

        return [
            'success' => true,
            'message' => "Article '{$name}' deleted",
            'id' => $id
        ];
    }

    private static function setArticleData($article, $data): void {
        if (isset($data->ingress)) {
            $article->ingress = $data->ingress;
        }

        if (isset($data->body)) {
            $article->body = $data->body;
        }
    }

    private static function formatArticleResponse($article): array {
        return [
            'id' => $article->id,
            'name' => $article->name,
            'title' => $article->title,
            'ingress' => $article->ingress,
            'body' => $article->body,
            'images' => $article->images && $article->images->count() ? $article->images->explode('url') : [],
        ];
    }
}
