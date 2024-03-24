<?php

namespace MagicLibrary\Security\Authentication\Domain\Exception;

final class UserException extends \Exception
{
    /**
     * @param string $email
     *
     * @return self
     */
    public static function emailAlreadyUsed(string $email): self
    {
        return new self(sprintf('email : [%s] is already used by another account', $email));
    }

    /**
     * @return self
     */
    public static function authenticationFailed(): self
    {
        return new self('Unable to authenticate');
    }

    /**
     * @return self
     */
    public static function invalidId(): self
    {
        return new self('Invalid user id');
    }
}
