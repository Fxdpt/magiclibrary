<?php

namespace MagicLibrary\Security\Authentication\Application\Repository;

use MagicLibrary\Security\Authentication\Domain\Model\User;

interface ReadUserRepositoryInterface
{
    /**
     * Find a user by his email.
     *
     * @param string $email
     *
     * @throws \Throwable
     *
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find a user by his authentication token.
     *
     * @param string $token
     *
     * @throws \Throwable
     *
     * @return User|null
     */
    public function findByToken(string $token): ?User;
}
