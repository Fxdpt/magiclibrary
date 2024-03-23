<?php

namespace MagicLibrary\Security\Authentication\Infrastructure\API\AddUser;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

final class AddUserRequest
{
    #[NotBlank()]
    #[Email()]
    public string $email = '';

    #[NotBlank()]
    public string $username = '';

    #[NotBlank()]
    public string $password = '';
}
