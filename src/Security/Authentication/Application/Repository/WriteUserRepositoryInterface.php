<?php

namespace MagicLibrary\Security\Authentication\Application\Repository;

use MagicLibrary\Security\Authentication\Domain\Model\User;

interface WriteUserRepositoryInterface
{
    /**
     * Add a user to data storage.
     *
     * @param User $user
     *
     * @throws \Throwable
     */
    public function add(User $user): void;
}
