<?php

namespace ProcessWire\Tests;

use PHPUnit\Framework\TestCase;
use ProcessWire\Character;
use ProcessWire\Page;
use ProcessWire\MockPages;
use ProcessWire\ApiNotFoundException;
use ProcessWire\ApiValidationException;

class CharacterTest extends TestCase {

    protected function setUp(): void {
        MockPages::clearMockPages();
    }

    protected function createMockCharacter(int $id, string $name): Page {
        $character = new Page();
        $character->id = $id;
        $character->name = $name;
        $character->title = ucfirst($name);
        $character->template->name = 'character';
        $character->ingress = 'Test ingress';
        $character->body = 'Test body';
        $character->attribute_strength = 10;
        $character->attribute_perception = 8;
        $character->attribute_endurance = 7;
        $character->attribute_charisma = 6;
        $character->attribute_intelligence = 9;
        $character->attribute_agility = 5;
        $character->attribute_luck = 4;
        $character->deutsche_marks = 100;

        // Add mock Things to inventory
        $sword = $this->createMockThing(1001, 'sword');
        $shield = $this->createMockThing(1002, 'shield');
        $character->inventory->add($sword);
        $character->inventory->add($shield);

        MockPages::addMockPage($character);
        return $character;
    }

    protected function createMockThing(int $id, string $name): Page {
        $thing = new Page();
        $thing->id = $id;
        $thing->name = $name;
        $thing->template->name = 'thing';
        MockPages::addMockPage($thing);
        return $thing;
    }

    public function testGetCharactersReturnsEmptyArrayWhenNoCharacters(): void {
        $result = Character::getCharacters();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('characters', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertEmpty($result['characters']);
    }

    public function testGetCharactersReturnsPaginationMeta(): void {
        $this->createMockCharacter(1, 'hero');
        $this->createMockCharacter(2, 'villain');

        $result = Character::getCharacters();

        $this->assertEquals(2, $result['pagination']['total']);
        $this->assertEquals(50, $result['pagination']['limit']);
        $this->assertEquals(0, $result['pagination']['offset']);
        $this->assertFalse($result['pagination']['hasMore']);
    }

    public function testGetCharactersRespectsPagination(): void {
        for ($i = 1; $i <= 5; $i++) {
            $this->createMockCharacter($i, "character-{$i}");
        }

        $data = (object) ['limit' => 2, 'offset' => 0];
        $result = Character::getCharacters($data);

        $this->assertCount(2, $result['characters']);
        $this->assertEquals(5, $result['pagination']['total']);
        $this->assertTrue($result['pagination']['hasMore']);
    }

    public function testGetCharacterReturnsCharacter(): void {
        $this->createMockCharacter(123, 'test-hero');

        $data = (object) ['id' => 123];
        $result = Character::getCharacter($data);

        $this->assertEquals(123, $result['id']);
        $this->assertEquals('test-hero', $result['name']);
        $this->assertEquals(10, $result['strength']);
        $this->assertEquals([1001, 1002], $result['inventory']);
    }

    public function testGetCharacterThrowsNotFoundForMissingCharacter(): void {
        $this->expectException(ApiNotFoundException::class);
        $this->expectExceptionMessage('Character not found');

        $data = (object) ['id' => 999];
        Character::getCharacter($data);
    }

    public function testGetCharacterThrowsApiValidationExceptionForWrongTemplate(): void {
        $page = new Page();
        $page->id = 100;
        $page->template->name = 'article';
        MockPages::addMockPage($page);

        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage('Page is not a character');

        $data = (object) ['id' => 100];
        Character::getCharacter($data);
    }

    public function testCreateCharacterRequiresName(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage('Missing required fields: name');

        $data = (object) ['title' => 'Test'];
        Character::createCharacter($data);
    }

    public function testCreateCharacterValidatesNameLength(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'name' must be at most 100 characters");

        $data = (object) ['name' => str_repeat('a', 101)];
        Character::createCharacter($data);
    }

    public function testCreateCharacterValidatesAttributeRange(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'strength' must be at most 100");

        $data = (object) [
            'name' => 'test',
            'strength' => 150
        ];
        Character::createCharacter($data);
    }

    public function testCreateCharacterValidatesInventoryIsArray(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'inventory' must be an array");

        $data = (object) [
            'name' => 'test',
            'inventory' => 'not an array'
        ];
        Character::createCharacter($data);
    }

    public function testCreateCharacterValidatesInventoryContainsIntegers(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'inventory' must contain only integers");

        $data = (object) [
            'name' => 'test',
            'inventory' => [1, 'sword', 3]
        ];
        Character::createCharacter($data);
    }

    public function testCreateCharacterAcceptsIntegerInventory(): void {
        // Create mock Things that will be added to inventory
        $this->createMockThing(101, 'item1');
        $this->createMockThing(102, 'item2');
        $this->createMockThing(103, 'item3');

        $data = (object) [
            'name' => 'test-character',
            'inventory' => [101, 102, 103]
        ];

        $result = Character::createCharacter($data);

        $this->assertEquals([101, 102, 103], $result['inventory']);
    }

    public function testCreateCharacterValidatesImagesFormat(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("contains invalid base64 data");

        $data = (object) [
            'name' => 'test',
            'images' => ['not valid base64!!!']
        ];
        Character::createCharacter($data);
    }

    public function testDeleteCharacterThrowsNotFoundForMissingCharacter(): void {
        $this->expectException(ApiNotFoundException::class);

        $data = (object) ['id' => 999];
        Character::deleteCharacter($data);
    }

    public function testDeleteCharacterReturnsSuccessResponse(): void {
        $this->createMockCharacter(456, 'doomed-hero');

        $data = (object) ['id' => 456];
        $result = Character::deleteCharacter($data);

        $this->assertTrue($result['success']);
        $this->assertEquals(456, $result['id']);
        $this->assertStringContainsString('doomed-hero', $result['message']);
    }

    public function testFormatCharacterResponseReturnsInventoryIds(): void {
        $character = $this->createMockCharacter(1, 'test');

        $data = (object) ['id' => 1];
        $result = Character::getCharacter($data);

        // Should return IDs of Things in inventory
        $this->assertEquals([1001, 1002], $result['inventory']);
    }
}
