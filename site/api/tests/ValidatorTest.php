<?php

namespace ProcessWire\Tests;

use PHPUnit\Framework\TestCase;
use ProcessWire\Validator;
use ProcessWire\ApiValidationException;

class ValidatorTest extends TestCase {

    public function testValidateRequiredPassesWithAllFields(): void {
        $data = (object) ['name' => 'Test', 'email' => 'test@example.com'];

        // Should not throw
        Validator::validateRequired($data, ['name', 'email']);
        $this->assertTrue(true);
    }

    public function testValidateRequiredThrowsOnMissingField(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage('Missing required fields: email');

        $data = (object) ['name' => 'Test'];
        Validator::validateRequired($data, ['name', 'email']);
    }

    public function testValidateRequiredThrowsOnEmptyString(): void {
        $this->expectException(ApiValidationException::class);

        $data = (object) ['name' => ''];
        Validator::validateRequired($data, ['name']);
    }

    public function testValidateRequiredThrowsOnWhitespaceOnly(): void {
        $this->expectException(ApiValidationException::class);

        $data = (object) ['name' => '   '];
        Validator::validateRequired($data, ['name']);
    }

    public function testValidateStringPasses(): void {
        Validator::validateString('Hello World', 'field', 1, 100);
        $this->assertTrue(true);
    }

    public function testValidateStringThrowsOnNonString(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'field' must be a string");

        Validator::validateString(123, 'field');
    }

    public function testValidateStringThrowsOnTooShort(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'name' must be at least 5 characters");

        Validator::validateString('Hi', 'name', 5, 100);
    }

    public function testValidateStringThrowsOnTooLong(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'name' must be at most 5 characters");

        Validator::validateString('Hello World', 'name', 1, 5);
    }

    public function testValidateIntPasses(): void {
        Validator::validateInt(50, 'field', 0, 100);
        $this->assertTrue(true);
    }

    public function testValidateIntPassesWithStringNumber(): void {
        Validator::validateInt('42', 'field', 0, 100);
        $this->assertTrue(true);
    }

    public function testValidateIntThrowsOnNonNumeric(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'age' must be a number");

        Validator::validateInt('abc', 'age');
    }

    public function testValidateIntThrowsOnBelowMin(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'score' must be at least 0");

        Validator::validateInt(-5, 'score', 0, 100);
    }

    public function testValidateIntThrowsOnAboveMax(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'score' must be at most 100");

        Validator::validateInt(150, 'score', 0, 100);
    }

    public function testValidateArrayPasses(): void {
        Validator::validateArray(['a', 'b', 'c'], 'items');
        $this->assertTrue(true);
    }

    public function testValidateArrayThrowsOnNonArray(): void {
        $this->expectException(ApiValidationException::class);
        $this->expectExceptionMessage("Field 'items' must be an array");

        Validator::validateArray('not an array', 'items');
    }

    public function testGetPaginationParamsDefaults(): void {
        $params = Validator::getPaginationParams(new \stdClass());

        $this->assertEquals(50, $params['limit']);
        $this->assertEquals(0, $params['offset']);
    }

    public function testGetPaginationParamsCustomValues(): void {
        $data = (object) ['limit' => 25, 'offset' => 50];
        $params = Validator::getPaginationParams($data);

        $this->assertEquals(25, $params['limit']);
        $this->assertEquals(50, $params['offset']);
    }

    public function testGetPaginationParamsClampsLimit(): void {
        $data = (object) ['limit' => 500];
        $params = Validator::getPaginationParams($data);

        $this->assertEquals(100, $params['limit']); // Max limit
    }

    public function testGetPaginationParamsEnsuresNonNegativeOffset(): void {
        $data = (object) ['offset' => -10];
        $params = Validator::getPaginationParams($data);

        $this->assertEquals(0, $params['offset']);
    }

    public function testBuildPaginationMeta(): void {
        $meta = Validator::buildPaginationMeta(150, 50, 0);

        $this->assertEquals(150, $meta['total']);
        $this->assertEquals(50, $meta['limit']);
        $this->assertEquals(0, $meta['offset']);
        $this->assertTrue($meta['hasMore']);
    }

    public function testBuildPaginationMetaNoMore(): void {
        $meta = Validator::buildPaginationMeta(30, 50, 0);

        $this->assertFalse($meta['hasMore']);
    }

    public function testDecodeInventoryEmpty(): void {
        $this->assertEquals([], Validator::decodeInventory(null));
        $this->assertEquals([], Validator::decodeInventory(''));
    }

    public function testDecodeInventoryJson(): void {
        $json = '["sword", "shield", "potion"]';
        $result = Validator::decodeInventory($json);

        $this->assertEquals(['sword', 'shield', 'potion'], $result);
    }

    public function testDecodeInventoryLegacyPipeDelimited(): void {
        $legacy = 'sword|shield|potion';
        $result = Validator::decodeInventory($legacy);

        $this->assertEquals(['sword', 'shield', 'potion'], $result);
    }

    public function testEncodeInventory(): void {
        $inventory = ['sword', 'shield', 'potion'];
        $result = Validator::encodeInventory($inventory);

        $this->assertEquals('["sword","shield","potion"]', $result);
    }

    public function testEncodeInventoryReindexes(): void {
        $inventory = [2 => 'sword', 5 => 'shield'];
        $result = Validator::encodeInventory($inventory);

        // Should reindex to 0-based array
        $this->assertEquals('["sword","shield"]', $result);
    }
}
