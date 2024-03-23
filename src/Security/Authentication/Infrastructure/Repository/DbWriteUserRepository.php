<?php

namespace MagicLibrary\Security\Authentication\Infrastructure\Repository;

use MagicLibrary\Common\DatabaseConnection;
use MagicLibrary\Security\Authentication\Application\Repository\WriteUserRepositoryInterface;
use MagicLibrary\Security\Authentication\Domain\Model\User;

final class DbWriteUserRepository implements WriteUserRepositoryInterface
{
    public function __construct(private readonly DatabaseConnection $db)
    {
    }

    public function add(User $user): void
    {
        $statement = $this->db->prepare(
            <<<SQL
                INSERT INTO `user` (email, username, password, roles) VALUES (:email, :username, :password, :roles)
                SQL
        );
        $statement->bindValue(':username', $user->getUsername(), \PDO::PARAM_STR);
        $statement->bindValue(':email', $user->getEmail(), \PDO::PARAM_STR);
        $statement->bindValue(':password', $user->getPassword(), \PDO::PARAM_STR);
        $statement->bindValue(':roles', implode(',', $user->getRoles()), \PDO::PARAM_STR);

        $statement->execute();
    }
}
