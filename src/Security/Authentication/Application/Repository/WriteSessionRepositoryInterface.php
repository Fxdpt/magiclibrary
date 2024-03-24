<?php

namespace MagicLibrary\Security\Authentication\Application\Repository;

use MagicLibrary\Security\Authentication\Domain\Model\Session;
use MagicLibrary\Security\Authentication\Domain\Model\User;

interface WriteSessionRepositoryInterface
{
    /**
     * Create session in data storage.
     *
     * @param Session $session
     *
     * @throws \Throwable
     */
    public function add(Session $session): void;

    /**
     * Refresh expiration of token
     *
     * @param User $user
     *
     * @throws \Throwable
     */
    public function refresh(User $user): void;

    /**
     * Delete existing token of given user
     *
     * @param User $user
     *
     * @throws \Throwable
     */
    public function delete(User $user): void;
}
