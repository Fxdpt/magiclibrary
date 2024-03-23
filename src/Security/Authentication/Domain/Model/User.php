<?php

namespace MagicLibrary\Security\Authentication\Domain\Model;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    private string $password;

    /**
     * Undocumented function
     *
     * @param string $email
     * @param string $username
     * @param string[] $roles
     */
    public function __construct(
        private readonly string $email,
        private readonly string $username,
        private array $roles,
    ) {
    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

 /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // guarantee every user at least has ROLE_USER
        $this->roles[] = self::ROLE_USER;

        return array_unique($this->roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
