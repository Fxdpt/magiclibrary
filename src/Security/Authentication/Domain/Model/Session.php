<?php

namespace MagicLibrary\Security\Authentication\Domain\Model;

final class Session
{
    public function __construct(
        private readonly int $userId,
        private readonly string $token,
        private readonly \DateTimeImmutable $expireAt
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpireAt(): \DateTimeImmutable
    {
        return $this->expireAt;
    }
}
