<?php

namespace ProcessWire;

require_once __DIR__ . '/ApiException.php';
require_once __DIR__ . '/Validator.php';

class Character {

    private const TEMPLATE_NAME = 'character';
    private const TEMPLATE_PARENT = 'characters';

    public static function getCharacters($data = null) {
        $pagination = Validator::getPaginationParams($data ?? new \stdClass());

        $allCharacters = wire('pages')->find("template=" . self::TEMPLATE_NAME);
        $total = $allCharacters->count();

        // Apply pagination
        $characters = $allCharacters->slice($pagination['offset'], $pagination['limit']);

        $response = [
            'characters' => [],
            'pagination' => Validator::buildPaginationMeta($total, $pagination['limit'], $pagination['offset'])
        ];

        foreach ($characters as $character) {
            $response['characters'][] = self::formatCharacterResponse($character, true);
        }

        return $response;
    }

    public static function getCharacter($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

        $character = wire('pages')->get($data->id);

        if (!$character->id) {
            throw new ApiNotFoundException('Character');
        }

        if ($character->template->name !== self::TEMPLATE_NAME) {
            throw new ApiValidationException('Page is not a character');
        }

        return self::formatCharacterResponse($character);
    }

    public static function createCharacter($data) {
        Validator::validateRequired($data, ['name']);
        Validator::validateString($data->name, 'name', 1, 100);

        if (isset($data->title)) {
            Validator::validateString($data->title, 'title', 0, 200);
        }

        self::validateCharacterAttributes($data);

        // Create new character page
        $charactersParent = wire('pages')->get('template=' . self::TEMPLATE_PARENT);
        if (!$charactersParent->id) {
            $charactersParent = wire('pages')->get('/');
        }

        $character = new \ProcessWire\Page();
        $character->template = self::TEMPLATE_NAME;
        $character->parent = $charactersParent;
        $character->name = wire('sanitizer')->pageName($data->name);
        $character->title = $data->title ?? $data->name;

        // Set character data using shared method
        self::setCharacterData($character, $data);

        // Save the character
        $character->save();

        if (!$character->id) {
            throw new ApiServerException('Failed to create character');
        }

        return self::formatCharacterResponse($character);
    }

    public static function updateCharacter($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

        $character = wire('pages')->get($data->id);

        if (!$character->id) {
            throw new ApiNotFoundException('Character');
        }

        if ($character->template->name !== self::TEMPLATE_NAME) {
            throw new ApiValidationException('Page is not a character');
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

        self::validateCharacterAttributes($requestBody);

        // Disable output formatting to allow modifications
        $character->of(false);

        // Update name and title if provided
        if (isset($requestBody->name) && !empty($requestBody->name)) {
            $character->name = wire('sanitizer')->pageName($requestBody->name);
        }

        if (isset($requestBody->title)) {
            $character->title = $requestBody->title;
        }

        // Set character data using shared method
        self::setCharacterData($character, $requestBody);

        // Save the character
        $character->save();

        return self::formatCharacterResponse($character);
    }

    public static function deleteCharacter($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

        $character = wire('pages')->get($data->id);

        if (!$character->id) {
            throw new ApiNotFoundException('Character');
        }

        if ($character->template->name !== self::TEMPLATE_NAME) {
            throw new ApiValidationException('Page is not a character');
        }

        $id = $character->id;
        $name = $character->name;

        // Delete the character
        wire('pages')->delete($character);

        return [
            'success' => true,
            'message' => "Character '{$name}' deleted",
            'id' => $id
        ];
    }

    private static function validateCharacterAttributes($data): void {
        $attributes = ['strength', 'perception', 'endurance', 'charisma', 'intelligence', 'agility', 'luck'];

        foreach ($attributes as $attr) {
            if (isset($data->$attr)) {
                Validator::validateInt($data->$attr, $attr, 0, 100);
            }
        }

        if (isset($data->deutsche_marks)) {
            Validator::validateInt($data->deutsche_marks, 'deutsche_marks', 0, null);
        }

        if (isset($data->inventory)) {
            Validator::validateIntArray($data->inventory, 'inventory');
        }

        if (isset($data->images) && !empty($data->images)) {
            $images = is_array($data->images) ? $data->images : [$data->images];
            foreach ($images as $index => $image) {
                if (!empty($image)) {
                    Validator::validateBase64Image($image, "images[{$index}]");
                }
            }
        }
    }

    private static function setCharacterData($character, $data): void {
        // Set optional fields if provided
        if (isset($data->bio) && !empty($data->bio)) {
            $character->ingress = $data->bio;
        }
        if (isset($data->ingress)) {
            $character->ingress = $data->ingress;
        }
        if (isset($data->body)) {
            $character->body = $data->body;
        }

        // Set attribute values if provided
        $attributes = [
            'strength' => 'attribute_strength',
            'perception' => 'attribute_perception',
            'endurance' => 'attribute_endurance',
            'charisma' => 'attribute_charisma',
            'intelligence' => 'attribute_intelligence',
            'agility' => 'attribute_agility',
            'luck' => 'attribute_luck'
        ];

        foreach ($attributes as $input => $field) {
            if (isset($data->$input)) {
                $character->$field = (int) $data->$input;
            }
        }

        // Set inventory (PageArray of Thing references)
        if (isset($data->inventory) && is_array($data->inventory)) {
            // Clear existing inventory
            if ($character->inventory && $character->inventory->count()) {
                $character->inventory->removeAll();
            }
            // Add each Thing by ID
            foreach ($data->inventory as $thingId) {
                $thing = wire('pages')->get((int) $thingId);
                if ($thing->id && $thing->template->name === 'thing') {
                    $character->inventory->add($thing);
                }
            }
        }

        if (isset($data->deutsche_marks)) {
            $character->deutsche_marks = (int) $data->deutsche_marks;
        }

        // Handle image upload (base64 string or array of base64 strings)
        if (isset($data->images) && !empty($data->images)) {
            $images = is_array($data->images) ? $data->images : [$data->images];
            $tempPaths = [];

            try {
                // Clear existing images if replacing
                if ($character->images && $character->images->count()) {
                    $character->images->deleteAll();
                }

                // Add each image
                foreach ($images as $index => $imageData) {
                    if (!empty($imageData)) {
                        $tempPath = Validator::saveBase64ToTemp($imageData, "images[{$index}]");
                        $tempPaths[] = $tempPath;
                        $character->images->add($tempPath);
                    }
                }
            } finally {
                // Clean up temp files
                foreach ($tempPaths as $tempPath) {
                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }
                }
            }
        }
    }

    private static function formatCharacterResponse($character, bool $forList = false): array {
        // Get portrait URL (first image in images field)
        $portrait = null;
        if ($character->images && $character->images->count()) {
            $portrait = $character->images->first()->url;
        }

        $response = [
            'id' => $character->id,
            'name' => $character->name,
            'title' => $character->title,
            'ingress' => $character->ingress,
            'body' => $character->body,
            'portrait' => $portrait,
            'images' => $character->images && $character->images->count() ? $character->images->explode('url') : [],
            'strength' => (int) $character->attribute_strength,
            'perception' => (int) $character->attribute_perception,
            'endurance' => (int) $character->attribute_endurance,
            'charisma' => (int) $character->attribute_charisma,
            'intelligence' => (int) $character->attribute_intelligence,
            'agility' => (int) $character->attribute_agility,
            'luck' => (int) $character->attribute_luck,
            'inventory' => $character->inventory && $character->inventory->count() ? $character->inventory->explode('id') : [],
            'deutsche_marks' => (int) $character->deutsche_marks,
        ];

        return $response;
    }
}
