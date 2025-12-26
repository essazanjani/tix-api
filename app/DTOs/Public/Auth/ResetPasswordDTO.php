<?php

namespace App\DTOs\Public\Auth;

class ResetPasswordDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    )
    {}


    public static function fromRequest(array $data): static
    {
        return new static(
            $data['email'],
            $data['password']
        );
    }
}
