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
				'strength' => $character->attribute_strength,
				'perception' => $character->attribute_perception,
				'endurance' => $character->attribute_endurance,
				'charisma' => $character->attribute_charisma,
				'intelligence' => $character->attribute_intelligence,
				'agility' => $character->attribute_agility,
				'luck' => $character->attribute_luck,
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
		$response->strength = $character->attribute_strength;
		$response->perception = $character->attribute_perception;
		$response->endurance = $character->attribute_endurance;
		$response->charisma = $character->attribute_charisma;
		$response->intelligence = $character->attribute_intelligence;
		$response->agility = $character->attribute_agility;
		$response->luck = $character->attribute_luck;

		return $response;
	}
}