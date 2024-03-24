<?php

namespace MagicLibrary\Security\Authentication\Infrastructure\API\Login;

use Symfony\Component\Validator\Constraints\NotBlank;

final class LoginRequest
{
    #[NotBlank()]
    public string $email = '';

    #[NotBlank()]
    public string $password = '';
}