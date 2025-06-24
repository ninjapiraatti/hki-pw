<?php

namespace ProcessWire;

class Thing {
    public static function getThings() {
        $response = [
            'things' => []
        ];

        foreach (wire('pages')->find("template=thing") as $thing) {
            $attributeEffectsArray = [];
            foreach($thing->attribute_effects as $attributeEffect) {
                $attributeEffectsArray[] = [
                    'target' => $attributeEffect->attribute_effect_target->title,
                    'strength' => $attributeEffect->effect_strength,
                ];
            }

            $skillEffectsArray = [];
            foreach($thing->skill_effects as $skillEffect) {
                $skillEffectsArray[] = [
                    'target' => $skillEffect->skill_effect_target,
                    'strength' => $skillEffect->effect_strength,
                ];
            }
            
            array_push($response['things'], [
                'id' => $thing->id,
                'name' => $thing->name,
                'title' => $thing->title,
                'ingress' => $thing->ingress,
                'body' => $thing->body,
                'images' => $thing->images->count() ? $thing->images->explode('url') : [],
                'damage' => $thing->damage,
                'attributeEffects' => $attributeEffectsArray,
                'skillEffects' => $skillEffectsArray,
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
        
        $attributeEffectsArray = [];
        foreach($thing->attribute_effects as $attributeEffect) {
            $attributeEffectsArray[] = [
                'target' => $attributeEffect->attribute_effect_target->title,
                'strength' => $attributeEffect->effect_strength,
            ];
        }
        $response->attributeEffects = $attributeEffectsArray;

        $skillEffectsArray = [];
        foreach($thing->skill_effects as $skillEffect) {
            $skillEffectsArray[] = [
                'target' => $skillEffect->skill_effect_target,
                'strength' => $skillEffect->effect_strength,
            ];
        }
        $response->skillEffects = $skillEffectsArray;

        return $response;
    }
}