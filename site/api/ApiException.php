<?php

namespace ProcessWire;

/**
 * Base API exception with HTTP status code support
 */
class ApiException extends \Exception {
    protected int $statusCode;
    protected array $errors;

    public function __construct(string $message, int $statusCode = 500, array $errors = []) {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}

/**
 * 404 Not Found exception
 */
class ApiNotFoundException extends ApiException {
    public function __construct(string $resource = 'Resource') {
        parent::__construct("{$resource} not found", 404);
    }
}

/**
 * 400 Bad Request - validation errors
 */
class ApiValidationException extends ApiException {
    public function __construct(string $message, array $errors = []) {
        parent::__construct($message, 400, $errors);
    }
}

/**
 * 500 Internal Server Error
 */
class ApiServerException extends ApiException {
    public function __construct(string $message = 'Internal server error') {
        parent::__construct($message, 500);
    }
}
