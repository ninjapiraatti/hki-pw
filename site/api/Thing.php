<?php

namespace ProcessWire;

require_once __DIR__ . '/ApiException.php';
require_once __DIR__ . '/Validator.php';

class Thing {

    private const TEMPLATE_NAME = 'thing';
    private const TEMPLATE_PARENT = 'things';

    public static function getThings($data = null) {
        $pagination = Validator::getPaginationParams($data ?? new \stdClass());

        $allThings = wire('pages')->find("template=" . self::TEMPLATE_NAME);
        $total = $allThings->count();

        // Apply pagination
        $things = $allThings->slice($pagination['offset'], $pagination['limit']);

        $response = [
            'things' => [],
            'pagination' => Validator::buildPaginationMeta($total, $pagination['limit'], $pagination['offset'])
        ];

        foreach ($things as $thing) {
            $response['things'][] = self::formatThingResponse($thing);
        }

        return $response;
    }

    public static function getThing($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

        $thing = wire('pages')->get($data->id);

        if (!$thing->id) {
            throw new ApiNotFoundException('Thing');
        }

        if ($thing->template->name !== self::TEMPLATE_NAME) {
            throw new ApiValidationException('Page is not a thing');
        }

        return self::formatThingResponse($thing);
    }

    public static function createThing($data) {
        Validator::validateRequired($data, ['name']);
        Validator::validateString($data->name, 'name', 1, 100);

        if (isset($data->title)) {
            Validator::validateString($data->title, 'title', 0, 200);
        }

        if (isset($data->damage)) {
            Validator::validateInt($data->damage, 'damage', 0, null);
        }

        // Create new thing page
        $thingsParent = wire('pages')->get('template=' . self::TEMPLATE_PARENT);
        if (!$thingsParent->id) {
            $thingsParent = wire('pages')->get('/');
        }

        $thing = new \ProcessWire\Page();
        $thing->template = self::TEMPLATE_NAME;
        $thing->parent = $thingsParent;
        $thing->name = wire('sanitizer')->pageName($data->name);
        $thing->title = $data->title ?? $data->name;

        // Set thing data
        self::setThingData($thing, $data);

        // Save the thing
        $thing->save();

        if (!$thing->id) {
            throw new ApiServerException('Failed to create thing');
        }

        return self::formatThingResponse($thing);
    }

    public static function updateThing($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

        $thing = wire('pages')->get($data->id);

        if (!$thing->id) {
            throw new ApiNotFoundException('Thing');
        }

        if ($thing->template->name !== self::TEMPLATE_NAME) {
            throw new ApiValidationException('Page is not a thing');
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

        if (isset($requestBody->damage)) {
            Validator::validateInt($requestBody->damage, 'damage', 0, null);
        }

        // Disable output formatting to allow modifications
        $thing->of(false);

        // Update name and title if provided
        if (isset($requestBody->name) && !empty($requestBody->name)) {
            $thing->name = wire('sanitizer')->pageName($requestBody->name);
        }

        if (isset($requestBody->title)) {
            $thing->title = $requestBody->title;
        }

        // Set thing data
        self::setThingData($thing, $requestBody);

        // Save the thing
        $thing->save();

        return self::formatThingResponse($thing);
    }

    public static function deleteThing($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

        $thing = wire('pages')->get($data->id);

        if (!$thing->id) {
            throw new ApiNotFoundException('Thing');
        }

        if ($thing->template->name !== self::TEMPLATE_NAME) {
            throw new ApiValidationException('Page is not a thing');
        }

        $id = $thing->id;
        $name = $thing->name;

        // Delete the thing
        wire('pages')->delete($thing);

        return [
            'success' => true,
            'message' => "Thing '{$name}' deleted",
            'id' => $id
        ];
    }

    private static function setThingData($thing, $data): void {
        if (isset($data->ingress)) {
            $thing->ingress = $data->ingress;
        }

        if (isset($data->body)) {
            $thing->body = $data->body;
        }

        if (isset($data->damage)) {
            $thing->damage = (int) $data->damage;
        }
    }

    private static function formatAttributeEffects($thing): array {
        $effects = [];
        if ($thing->attribute_effects) {
            foreach ($thing->attribute_effects as $effect) {
                $effects[] = [
                    'target' => $effect->attribute_effect_target ? $effect->attribute_effect_target->title : null,
                    'strength' => $effect->effect_strength,
                ];
            }
        }
        return $effects;
    }

    private static function formatSkillEffects($thing): array {
        $effects = [];
        if ($thing->skill_effects) {
            foreach ($thing->skill_effects as $effect) {
                $effects[] = [
                    'target' => $effect->skill_effect_target,
                    'strength' => $effect->effect_strength,
                ];
            }
        }
        return $effects;
    }

    private static function formatThingResponse($thing): array {
        return [
            'id' => $thing->id,
            'name' => $thing->name,
            'title' => $thing->title,
            'ingress' => $thing->ingress,
            'body' => $thing->body,
            'images' => $thing->images && $thing->images->count() ? $thing->images->explode('url') : [],
            'damage' => (int) $thing->damage,
            'attributeEffects' => self::formatAttributeEffects($thing),
            'skillEffects' => self::formatSkillEffects($thing),
        ];
    }
}
