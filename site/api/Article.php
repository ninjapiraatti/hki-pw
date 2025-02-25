<?php

namespace ProcessWire;

class Article {
	public static function getArticles() {
		$response = [
			'articles' => []
		];

		foreach (wire('pages')->find("template=article") as $article) {
			array_push($response['articles'], [
				'id' => $article->id,
				'name' => $article->name,
        'title' => $article->title,
        'body' => $article->body,
			]);
		}

		return $response;
	}

	public static function getArticle($data) {
		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

		$response = new \StdClass();
		$article = wire('pages')->get($data->id);

		if (!$article->id) {
			throw new \Exception('Article not found', 404);
		}

		$response->id = $article->id;
		$response->name = $article->name;
		$response->title = $article->title;
		$response->body = $article->body;
		$response->images = $article->images->explode('url');

		return $response;
	}
}