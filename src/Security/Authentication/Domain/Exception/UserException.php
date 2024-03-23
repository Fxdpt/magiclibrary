<?php

namespace MagicLibrary\Security\Authentication\Domain\Exception;

final class UserException extends \Exception
{
    public static function emailAlreadyUsed(string $email): self
    {
        return new self(sprintf('email : [%s] is already used by another account', $email));
    }
}
