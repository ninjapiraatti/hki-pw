# HKI2050 Backend

ProcessWire-based API backend for an RPG/game content management system using the AppApi module.

## Tech Stack

- **ProcessWire CMS** - Page-based content management
- **AppApi module** - REST API layer with JWT/API key authentication
- **PHPUnit 12.5+** - Testing with mocked ProcessWire environment

## Project Structure

```
site/api/
├── Routes.php          # All API route definitions
├── ApiException.php    # Exception classes (404, 400, 500)
├── Validator.php       # Input validation and parsing utilities
├── Article.php         # Article CRUD operations
├── Character.php       # Character CRUD with attributes/inventory
├── Thing.php           # Thing/item CRUD with effects
└── tests/
    ├── bootstrap.php   # Mock ProcessWire environment
    └── *Test.php       # Test files
```

## API Resources

| Resource | Template | Parent Path | Key Fields |
|----------|----------|-------------|------------|
| Articles | `article` | `/articles/` | title, ingress, body, images |
| Characters | `character` | `/characters/` | 7 SPECIAL attributes, inventory, deutsche_marks |
| Things | `thing` | `/things/` | damage, attribute_effects, skill_effects |

## Conventions

### Route Definitions
Routes use FastRoute syntax in `Routes.php`:
```php
['GET', '{id:\d+}', Article::class, 'getArticle', ['auth' => true]]
```

### Handler Pattern
All handlers are static methods that receive `$data` object from AppApi:
```php
public static function getArticle($data): array {
    $params = AppApiHelper::checkAndSanitizeRequiredParameters($data, ['id|int']);
    // ...
    return ['article' => $formatted];
}
```

### Validation
Use `Validator` class methods, not inline validation:
- `Validator::validateRequired($data, ['field1', 'field2'])`
- `Validator::validateString($value, 'fieldName', $min, $max)`
- `Validator::validateInt($value, 'fieldName', $min, $max)`

### Exceptions
- `ApiNotFoundException` - 404, resource not found
- `ApiValidationException` - 400, bad input
- `ApiServerException` - 500, internal errors

### Pagination
List endpoints support `?limit=` (max 100) and `?offset=`. Use:
```php
$params = Validator::getPaginationParams($data);
```

### ProcessWire Field Access
Disable output formatting before updates:
```php
$page->of(false);
$page->set('field', $value);
$page->save();
```

## Character Attributes
Maps to `attribute_*` fields: strength, perception, endurance, charisma, intelligence, agility, luck (0-100 range).

## Inventory Format
- Input: Array of integers (Thing IDs), e.g., `[101, 102, 103]`
- Storage: ProcessWire PageArray (references to Thing pages)
- Response: Array of Thing IDs
- Invalid IDs or non-Thing pages are silently skipped

## Image Upload
Characters support image upload via base64 encoding (single string or array):
```json
{
  "name": "hero",
  "images": ["data:image/png;base64,iVBORw0KGgo..."]
}
```
- Accepts: jpeg, png, gif, webp
- Data URI prefix optional (auto-detected from binary)
- Stored in ProcessWire `images` field (replaces existing images)
- Response includes `portrait` (first image URL) and `images` array

## Running Tests

```bash
cd site/api
./vendor/bin/phpunit
```

Tests use a mocked ProcessWire environment defined in `tests/bootstrap.php`. No actual PW instance required.

## Adding New Endpoints

1. Create handler class in `site/api/` with static methods
2. Add routes to `Routes.php` with appropriate auth flag
3. Use `Validator` for input validation
4. Throw appropriate `Api*Exception` on errors
5. Return associative array (auto-serialized to JSON)
6. Add tests with mock pages in `tests/`
