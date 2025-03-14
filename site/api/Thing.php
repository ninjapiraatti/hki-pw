<?php

namespace ProcessWire;

class Thing {
    public static function getThings() {
        $response = [
            'things' => []
        ];

        foreach (wire('pages')->find("template=thing") as $thing) {
            $effectsArray = [];
            foreach($thing->effects as $effect) {
                $effectsArray[] = [
                    'target' => $effect->effect_target->title,
                    'strength' => $effect->effect_strength,
                ];
            }
            
            array_push($response['things'], [
                'id' => $thing->id,
                'name' => $thing->name,
                'title' => $thing->title,
                'body' => $thing->body,
                'images' => $thing->images->count() ? $thing->images->explode('url') : [],
                'damage' => $thing->damage,
                'effects' => $effectsArray,
            ]);
        }

        return $response;
    }

    public static function getThing($data) {
        $data = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);

        $response = new \StdClass();
        $thing = wire('pages')->get($data->id);

        if (!$thing->id) {
            throw new \Exception('thing not found', 404);
        }

        $response->id = $thing->id;
        $response->name = $thing->name;
        $response->title = $thing->title;
        $response->body = $thing->body;
        $response->images = $thing->images->count() ? $thing->images->explode('url') : [];
        
        $effectsArray = [];
        foreach($thing->effects as $effect) {
            $effectsArray[] = [
                'target' => $effect->effect_target->title,
                'strength' => $effect->effect_strength,
            ];
        }
        $response->effects = $effectsArray;

        return $response;
    }
}