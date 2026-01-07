<?php

namespace ProcessWire;

require_once __DIR__ . '/ApiException.php';

class Validator {

    /**
     * Validate that required fields exist and are not empty
     *
     * @param object|array $data Input data
     * @param array $fields Field names to check
     * @throws ValidationException If any required field is missing
     */
    public static function validateRequired($data, array $fields): void {
        $data = (object) $data;
        $missing = [];

        foreach ($fields as $field) {
            if (!isset($data->$field) || (is_string($data->$field) && trim($data->$field) === '')) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new ApiValidationException(
                'Missing required fields: ' . implode(', ', $missing),
                ['missing_fields' => $missing]
            );
        }
    }

    /**
     * Validate string length
     *
     * @param mixed $value Value to validate
     * @param string $field Field name for error messages
     * @param int $minLen Minimum length (0 = no minimum)
     * @param int $maxLen Maximum length (0 = no maximum)
     * @throws ValidationException If validation fails
     */
    public static function validateString($value, string $field, int $minLen = 0, int $maxLen = 0): void {
        if (!is_string($value)) {
            throw new ApiValidationException(
                "Field '{$field}' must be a string",
                ['field' => $field, 'expected' => 'string']
            );
        }

        $len = mb_strlen($value);

        if ($minLen > 0 && $len < $minLen) {
            throw new ApiValidationException(
                "Field '{$field}' must be at least {$minLen} characters",
                ['field' => $field, 'min_length' => $minLen, 'actual_length' => $len]
            );
        }

        if ($maxLen > 0 && $len > $maxLen) {
            throw new ApiValidationException(
                "Field '{$field}' must be at most {$maxLen} characters",
                ['field' => $field, 'max_length' => $maxLen, 'actual_length' => $len]
            );
        }
    }

    /**
     * Validate integer value and range
     *
     * @param mixed $value Value to validate
     * @param string $field Field name for error messages
     * @param int|null $min Minimum value (null = no minimum)
     * @param int|null $max Maximum value (null = no maximum)
     * @throws ValidationException If validation fails
     */
    public static function validateInt($value, string $field, ?int $min = null, ?int $max = null): void {
        if (!is_numeric($value)) {
            throw new ApiValidationException(
                "Field '{$field}' must be a number",
                ['field' => $field, 'expected' => 'integer']
            );
        }

        $intVal = (int) $value;

        if ($min !== null && $intVal < $min) {
            throw new ApiValidationException(
                "Field '{$field}' must be at least {$min}",
                ['field' => $field, 'min' => $min, 'actual' => $intVal]
            );
        }

        if ($max !== null && $intVal > $max) {
            throw new ApiValidationException(
                "Field '{$field}' must be at most {$max}",
                ['field' => $field, 'max' => $max, 'actual' => $intVal]
            );
        }
    }

    /**
     * Validate that value is an array
     *
     * @param mixed $value Value to validate
     * @param string $field Field name for error messages
     * @throws ValidationException If validation fails
     */
    public static function validateArray($value, string $field): void {
        if (!is_array($value)) {
            throw new ApiValidationException(
                "Field '{$field}' must be an array",
                ['field' => $field, 'expected' => 'array']
            );
        }
    }

    /**
     * Get pagination parameters from request data
     *
     * @param object|array $data Request data
     * @param int $defaultLimit Default items per page
     * @param int $maxLimit Maximum allowed limit
     * @return array ['limit' => int, 'offset' => int]
     */
    public static function getPaginationParams($data, int $defaultLimit = 50, int $maxLimit = 100): array {
        $data = (object) $data;

        $limit = isset($data->limit) ? (int) $data->limit : $defaultLimit;
        $offset = isset($data->offset) ? (int) $data->offset : 0;

        // Clamp limit to valid range
        $limit = max(1, min($limit, $maxLimit));

        // Ensure offset is non-negative
        $offset = max(0, $offset);

        return [
            'limit' => $limit,
            'offset' => $offset
        ];
    }

    /**
     * Build pagination response metadata
     *
     * @param int $total Total number of items
     * @param int $limit Items per page
     * @param int $offset Current offset
     * @return array Pagination metadata
     */
    public static function buildPaginationMeta(int $total, int $limit, int $offset): array {
        return [
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'hasMore' => ($offset + $limit) < $total
        ];
    }

    /**
     * Parse JSON from request body
     *
     * @return object Parsed JSON data
     * @throws ValidationException If JSON is invalid
     */
    public static function parseJsonBody(): object {
        $body = file_get_contents('php://input');

        if (empty($body)) {
            return new \stdClass();
        }

        $data = json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiValidationException(
                'Invalid JSON in request body: ' . json_last_error_msg(),
                ['json_error' => json_last_error_msg()]
            );
        }

        return $data ?: new \stdClass();
    }

    /**
     * Decode inventory field - supports both JSON and legacy pipe-delimited format
     *
     * @param string|null $value Stored inventory value
     * @return array Decoded inventory array
     */
    public static function decodeInventory(?string $value): array {
        if (empty($value)) {
            return [];
        }

        // Check if it's JSON format (starts with '[')
        if (str_starts_with(trim($value), '[')) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        // Legacy pipe-delimited format
        return array_filter(explode('|', $value), fn($item) => $item !== '');
    }

    /**
     * Encode inventory field as JSON
     *
     * @param array $inventory Inventory array
     * @return string JSON encoded inventory
     */
    public static function encodeInventory(array $inventory): string {
        return json_encode(array_values($inventory));
    }
}
