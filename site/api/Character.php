<?php

namespace ProcessWire;

class Character {
	public static function getCharacters() {
		$response = [
			'characters' => []
		];

		foreach (wire('pages')->find("template=character") as $character) {
			array_push($response['characters'], [
				'id' => $character->id,
				'name' => $character->name,
        'title' => $character->title,
				'ingress' => $character->ingress,
        'body' => $character->body,
				'images' => $character->images->count() ? $character->images->explode('url') : [],
			]);
		}

		return $response;
	}

	public static function getCharacter($data) {
		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

		$response = new \StdClass();
		$character = wire('pages')->get($data->id);

		if (!$character->id) {
			throw new \Exception('Character not found', 404);
		}

		$response->id = $character->id;
		$response->name = $character->name;
		$response->title = $character->title;
		$response->body = $character->body;
		$response->images = $character->images->count() ? $character->images->explode('url') : [];

		return $response;
	}
}