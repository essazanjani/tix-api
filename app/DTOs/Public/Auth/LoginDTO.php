<?php

namespace App\DTOs\Public\Auth;

class LoginDTO
{
    public function __construct(
        public readonly string  $email,
        public readonly string  $password,
        public readonly ?string $remember_me
    )
    {
    }


    public static function fromRequest(array $data): static
    {
        return new static(
            $data['email'],
            $data['password'],
            $data['remember_me'] ?? false
        );
    }
}
