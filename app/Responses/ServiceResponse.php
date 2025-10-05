<?php

namespace App\Responses;

class ServiceResponse
{
    public function __construct(
        public bool  $success = true,
        public mixed $data = null,
        public array $errors = [],
    )
    {
    }

    public static function success(mixed $data): self
    {
        return new self(
            success: true,
            data: $data,
        );
    }

    public static function failed(array $errors): self
    {
        return new self(
            success: false,
            errors: $errors,
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'data' => $this->data,
            'errors' => $this->errors,
        ];
    }
}
