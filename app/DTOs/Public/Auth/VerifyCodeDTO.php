<?php

namespace App\DTOs\Public\Auth;

class VerifyCodeDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $code
    )
    {}


    public static function fromRequest(array $data): static
    {
        return new static(
            $data['email'],
            $data['code']
        );
    }
}
