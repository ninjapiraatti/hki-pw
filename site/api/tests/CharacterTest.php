<?php

namespace ProcessWire\Tests;

use PHPUnit\Framework\TestCase;
use ProcessWire\Character;
use ProcessWire\Page;
use ProcessWire\MockPages;
use ProcessWire\NotFoundException;
use ProcessWire\ValidationException;

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
        $character->inventory = '["sword","shield"]';
        $character->deutsche_marks = 100;

        MockPages::addMockPage($character);
        return $character;
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
        $this->assertEquals(['sword', 'shield'], $result['inventory']);
    }

    public function testGetCharacterThrowsNotFoundForMissingCharacter(): void {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Character not found');

        $data = (object) ['id' => 999];
        Character::getCharacter($data);
    }

    public function testGetCharacterThrowsValidationExceptionForWrongTemplate(): void {
        $page = new Page();
        $page->id = 100;
        $page->template->name = 'article';
        MockPages::addMockPage($page);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Page is not a character');

        $data = (object) ['id' => 100];
        Character::getCharacter($data);
    }

    public function testCreateCharacterRequiresName(): void {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Missing required fields: name');

        $data = (object) ['title' => 'Test'];
        Character::createCharacter($data);
    }

    public function testCreateCharacterValidatesNameLength(): void {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Field 'name' must be at most 100 characters");

        $data = (object) ['name' => str_repeat('a', 101)];
        Character::createCharacter($data);
    }

    public function testCreateCharacterValidatesAttributeRange(): void {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Field 'strength' must be at most 100");

        $data = (object) [
            'name' => 'test',
            'strength' => 150
        ];
        Character::createCharacter($data);
    }

    public function testCreateCharacterValidatesInventoryIsArray(): void {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Field 'inventory' must be an array");

        $data = (object) [
            'name' => 'test',
            'inventory' => 'not an array'
        ];
        Character::createCharacter($data);
    }

    public function testDeleteCharacterThrowsNotFoundForMissingCharacter(): void {
        $this->expectException(NotFoundException::class);

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

    public function testFormatCharacterResponseDecodesJsonInventory(): void {
        $character = $this->createMockCharacter(1, 'test');
        $character->inventory = '["item1","item2","item3"]';

        $data = (object) ['id' => 1];
        $result = Character::getCharacter($data);

        $this->assertEquals(['item1', 'item2', 'item3'], $result['inventory']);
    }

    public function testFormatCharacterResponseDecodesLegacyInventory(): void {
        $character = $this->createMockCharacter(2, 'test');
        $character->inventory = 'item1|item2|item3';

        $data = (object) ['id' => 2];
        $result = Character::getCharacter($data);

        $this->assertEquals(['item1', 'item2', 'item3'], $result['inventory']);
    }
}
