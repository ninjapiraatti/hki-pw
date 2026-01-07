<?php

namespace ProcessWire\Tests;

use PHPUnit\Framework\TestCase;
use ProcessWire\Thing;
use ProcessWire\Page;
use ProcessWire\MockPages;
use ProcessWire\ApiNotFoundException;
use ProcessWire\ApiValidationException;

class ThingTest extends TestCase {

    protected function setUp(): void {
        MockPages::clearMockPages();
    }

    protected function createMockThing(int $id, string $name): Page {
        $thing = new Page();
        $thing->id = $id;
        $thing->name = $name;
        $thing->title = ucfirst($name);
        $thing->template->name = 'thing';
        $thing->ingress = 'Test ingress';
        $thing->body = 'Test body content';
        $thing->damage = 10;

        MockPages::addMockPage($thing);
        return $thing;
    }

    public function testGetThingsReturnsEmptyArrayWhenNoThings(): void {
        $result = Thing::getThings();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('things', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertEmpty($result['things']);
    }

    public function testGetThingsReturnsPaginationMeta(): void {
        $this->createMockThing(1, 'sword');
        $this->createMockThing(2, 'shield');

        $result = Thing::getThings();

        $this->assertEquals(2, $result['pagination']['total']);
        $this->assertEquals(50, $result['pagination']['limit']);
        $this->assertEquals(0, $result['pagination']['offset']);
        $this->assertFalse($result['pagination']['hasMore']);
    }

    public function testGetThingsRespectsPagination(): void {
        for ($i = 1; $i <= 5; $i++) {
            $this->createMockThing($i, "item-{$i}");
        }

        $data = (object) ['limit' => 2, 'offset' => 0];
        $result = Thing::getThings($data);

        $this->assertCount(2, $result['things']);
        $this->assertEquals(5, $result['pagination']['total']);
        $this->assertTrue($result['pagination']['hasMore']);
    }

    public function testGetThingReturnsThing(): void {
        $this->createMockThing(123, 'magic-sword');

        $data = (object) ['id' => 123];
        $result = Thing::getThing($data);

        $this->assertEquals(123, $result['id']);
        $this->assertEquals('magic-sword', $result['name']);
        $this->assertEquals(10, $result['damage']);
    }

    public function testGetThingThrowsNotFoundForMissingThing(): void {
        $this->expectException(ApiNotFoundException::class);
        $this->expectExceptionMessage('Thing not found');

        $data = (object) ['id' => 999];
        Thing::getThing($data);
    }

    public function testGetThingThrowsApiValidationExceptionForWrongTemplate(): void {
        $page = new Page();
        $page->id = 100;
        $page->template->name = 'article';
        MockPages::addMockPage($page);

        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage('Page is not a thing');

        $data = (object) ['id' => 100];
        Thing::getThing($data);
    }

    public function testCreateThingRequiresName(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage('Missing required fields: name');

        $data = (object) ['title' => 'Test'];
        Thing::createThing($data);
    }

    public function testCreateThingValidatesNameLength(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'name' must be at most 100 characters");

        $data = (object) ['name' => str_repeat('a', 101)];
        Thing::createThing($data);
    }

    public function testCreateThingValidatesDamageIsNonNegative(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'damage' must be at least 0");

        $data = (object) [
            'name' => 'test',
            'damage' => -5
        ];
        Thing::createThing($data);
    }

    public function testDeleteThingThrowsNotFoundForMissingThing(): void {
        $this->expectException(ApiNotFoundException::class);

        $data = (object) ['id' => 999];
        Thing::deleteThing($data);
    }

    public function testDeleteThingReturnsSuccessResponse(): void {
        $this->createMockThing(456, 'broken-sword');

        $data = (object) ['id' => 456];
        $result = Thing::deleteThing($data);

        $this->assertTrue($result['success']);
        $this->assertEquals(456, $result['id']);
        $this->assertStringContainsString('broken-sword', $result['message']);
    }

    public function testFormatThingResponseIncludesAllFields(): void {
        $thing = $this->createMockThing(1, 'test');

        $data = (object) ['id' => 1];
        $result = Thing::getThing($data);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('ingress', $result);
        $this->assertArrayHasKey('body', $result);
        $this->assertArrayHasKey('images', $result);
        $this->assertArrayHasKey('damage', $result);
        $this->assertArrayHasKey('attributeEffects', $result);
        $this->assertArrayHasKey('skillEffects', $result);
    }

    public function testFormatThingResponseReturnsEmptyArraysForEffects(): void {
        $this->createMockThing(1, 'test');

        $data = (object) ['id' => 1];
        $result = Thing::getThing($data);

        $this->assertIsArray($result['attributeEffects']);
        $this->assertIsArray($result['skillEffects']);
        $this->assertEmpty($result['attributeEffects']);
        $this->assertEmpty($result['skillEffects']);
    }
}
