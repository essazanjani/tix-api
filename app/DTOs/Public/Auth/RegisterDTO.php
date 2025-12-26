<?php

namespace App\DTOs\Public\Auth;

class RegisterDTO
{
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly string $password
    )
    {}


    public static function fromRequest(array $data): static
    {
        return new static(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password']
        );
    }
}
