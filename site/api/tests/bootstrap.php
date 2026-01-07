<?php

/**
 * PHPUnit Bootstrap - Mocks ProcessWire dependencies for testing
 */

namespace ProcessWire;

// Mock wire() function
if (!function_exists('ProcessWire\wire')) {
    function wire($name = null) {
        static $services = [];

        if ($services === []) {
            $services = [
                'pages' => new MockPages(),
                'sanitizer' => new MockSanitizer(),
                'config' => new MockConfig(),
            ];
        }

        if ($name === null) {
            return (object) $services;
        }

        return $services[$name] ?? null;
    }
}

/**
 * Mock ProcessWire Page class
 */
class Page {
    public $id = 0;
    public $name = '';
    public $title = '';
    public $template;
    public $parent;
    public $ingress = '';
    public $body = '';
    public $images;
    public $inventory = '';
    public $deutsche_marks = 0;
    public $attribute_strength = 0;
    public $attribute_perception = 0;
    public $attribute_endurance = 0;
    public $attribute_charisma = 0;
    public $attribute_intelligence = 0;
    public $attribute_agility = 0;
    public $attribute_luck = 0;
    public $damage = 0;
    public $attribute_effects;
    public $skill_effects;

    private $outputFormatting = true;

    public function __construct() {
        $this->template = new MockTemplate();
        $this->images = new MockWireArray();
        $this->attribute_effects = new MockWireArray();
        $this->skill_effects = new MockWireArray();
    }

    public function save() {
        if ($this->id === 0) {
            $this->id = rand(1000, 9999);
        }
        return true;
    }

    public function of($value = null) {
        if ($value !== null) {
            $this->outputFormatting = $value;
        }
        return $this->outputFormatting;
    }
}

/**
 * Mock Template class
 */
class MockTemplate {
    public $name = '';
}

/**
 * Mock WireArray class for images, effects, etc.
 */
class MockWireArray implements \Countable, \IteratorAggregate {
    private $items = [];

    public function count(): int {
        return count($this->items);
    }

    public function getIterator(): \ArrayIterator {
        return new \ArrayIterator($this->items);
    }

    public function add($item) {
        $this->items[] = $item;
        return $this;
    }

    public function explode($field) {
        return array_map(fn($item) => $item->$field ?? '', $this->items);
    }
}

/**
 * Mock Pages class
 */
class MockPages {
    private static $pages = [];

    public function find($selector) {
        return new MockPageArray(array_values(self::$pages));
    }

    public function get($selector) {
        // Handle ID lookup
        if (is_numeric($selector)) {
            return self::$pages[$selector] ?? new Page();
        }

        // Handle template selector
        if (strpos($selector, 'template=') === 0) {
            $template = str_replace('template=', '', $selector);
            foreach (self::$pages as $page) {
                if ($page->template->name === $template) {
                    return $page;
                }
            }
        }

        // Return root page fallback
        if ($selector === '/') {
            $root = new Page();
            $root->id = 1;
            return $root;
        }

        return new Page();
    }

    public function delete($page) {
        unset(self::$pages[$page->id]);
        return true;
    }

    public static function addMockPage(Page $page) {
        self::$pages[$page->id] = $page;
    }

    public static function clearMockPages() {
        self::$pages = [];
    }
}

/**
 * Mock PageArray class
 */
class MockPageArray implements \Countable, \IteratorAggregate {
    private $items = [];

    public function __construct(array $items = []) {
        $this->items = $items;
    }

    public function count(): int {
        return count($this->items);
    }

    public function getIterator(): \ArrayIterator {
        return new \ArrayIterator($this->items);
    }

    public function slice($offset, $limit) {
        return new MockPageArray(array_slice($this->items, $offset, $limit));
    }
}

/**
 * Mock Sanitizer class
 */
class MockSanitizer {
    public function pageName($name) {
        return preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
    }
}

/**
 * Mock Config class
 */
class MockConfig {
    public $paths;

    public function __construct() {
        $this->paths = new \stdClass();
        $this->paths->AppApi = __DIR__ . '/../../';
    }
}

/**
 * Mock AppApiHelper class
 */
class AppApiHelper {
    public static function checkAndSanitizeRequiredParameters($data, $params) {
        $result = new \stdClass();

        foreach ($params as $param) {
            $parts = explode('|', $param);
            $name = $parts[0];
            $type = $parts[1] ?? 'string';

            if (isset($data->$name)) {
                if ($type === 'int') {
                    $result->$name = (int) $data->$name;
                } else {
                    $result->$name = $data->$name;
                }
            }
        }

        return $result;
    }
}

// Include the actual API classes
require_once __DIR__ . '/../ApiException.php';
require_once __DIR__ . '/../Validator.php';
require_once __DIR__ . '/../Character.php';
require_once __DIR__ . '/../Article.php';
require_once __DIR__ . '/../Thing.php';
