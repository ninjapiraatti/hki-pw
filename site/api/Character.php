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
	
	public static function createCharacter($data) {
		if (empty($data->name)) {
			throw new \Exception('Name is required in request body', 400);
		}

		// Create new character page
		$charactersParent = wire('pages')->get('template=characters');
		if (!$charactersParent->id) {
			$charactersParent = wire('pages')->get('/');
		}

		$character = new \ProcessWire\Page();
		$character->template = 'character';
		$character->parent = $charactersParent;
		$character->name = wire('sanitizer')->pageName($data->name);
		$character->title = $data->name; // Use name as title

		// Set character data using shared method
		self::setCharacterData($character, $data);

		// Save the character
		$character->save();

		if (!$character->id) {
			throw new \Exception('Failed to create character', 500);
		}

		// Return the created character data using shared method
		return self::formatCharacterResponse($character);
	}
	
	private static function setCharacterData($character, $data) {
		// Set optional fields if provided
		if (isset($data->bio) && !empty($data->bio)) {
			$character->ingress = $data->bio;
		}
		if (isset($data->body)) {
			$character->body = $data->body;
		}

		// Set attribute values if provided
		if (isset($data->strength)) {
			$character->attribute_strength = (int)$data->strength;
		}
		if (isset($data->perception)) {
			$character->attribute_perception = (int)$data->perception;
		}
		if (isset($data->endurance)) {
			$character->attribute_endurance = (int)$data->endurance;
		}
		if (isset($data->charisma)) {
			$character->attribute_charisma = (int)$data->charisma;
		}
		if (isset($data->intelligence)) {
			$character->attribute_intelligence = (int)$data->intelligence;
		}
		if (isset($data->agility)) {
			$character->attribute_agility = (int)$data->agility;
		}
		if (isset($data->luck)) {
			$character->attribute_luck = (int)$data->luck;
		}
	}
	
	private static function formatCharacterResponse($character) {
		$response = new \StdClass();
		$response->id = $character->id;
		$response->name = $character->name;
		$response->title = $character->title;
		$response->ingress = $character->ingress;
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
	
	public static function updateCharacter($data) {
		// Extract ID from URL parameters and validate it
		$data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);
		
		// Get the character to update
		$character = wire('pages')->get($data->id);
		
		if (!$character->id) {
			throw new \Exception('Character not found', 404);
		}
		
		if ($character->template->name !== 'character') {
			throw new \Exception('Page is not a character', 400);
		}
		
		// Get JSON data from request body
		$requestBody = json_decode(file_get_contents('php://input'));
		
		if (!$requestBody) {
			throw new \Exception('Invalid JSON data in request body', 400);
		}
		
		// Disable output formatting to allow modifications
		$character->of(false);
		
		// Update name and title if provided
		if (isset($requestBody->name) && !empty($requestBody->name)) {
			$character->name = wire('sanitizer')->pageName($requestBody->name);
			$character->title = $requestBody->name;
		}
		
		// Set character data using shared method
		self::setCharacterData($character, $requestBody);
		
		// Save the character
		$character->save();
		
		// Return the updated character data using shared method
		return self::formatCharacterResponse($character);
	}
}