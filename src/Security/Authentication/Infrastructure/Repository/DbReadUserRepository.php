<?php

namespace MagicLibrary\Security\Authentication\Infrastructure\Repository;

use MagicLibrary\Common\DatabaseConnection;
use MagicLibrary\Security\Authentication\Application\Repository\ReadUserRepositoryInterface;
use MagicLibrary\Security\Authentication\Domain\Model\User;

final class DbReadUserRepository implements ReadUserRepositoryInterface
{
    public function __construct(private readonly DatabaseConnection $db)
    {
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->db->prepare(
            <<<'SQL'
                SELECT * from user WHERE email=:email
                SQL
        );
        $statement->bindValue(':email', $email, \PDO::PARAM_STR);
        $statement->execute();

        $user = null;

        if (($result = $statement->fetch(\PDO::FETCH_ASSOC)) !== false) {
            /**
             * @var array{
             *  email: string,
             *  username: string,
             *  password: string,
             *  roles: string
             * } $result
             */
            $user = (new User(
                $result['email'],
                $result['username'],
                explode(',', $result['roles'])
            ))->setPassword($result['password']);
        }

        return $user;
    }
}
