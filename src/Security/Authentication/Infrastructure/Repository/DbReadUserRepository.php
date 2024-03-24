<?php

namespace MagicLibrary\Security\Authentication\Infrastructure\Repository;

use MagicLibrary\Common\DatabaseConnection;
use MagicLibrary\Security\Authentication\Application\Repository\ReadUserRepositoryInterface;
use MagicLibrary\Security\Authentication\Domain\Model\User;

final class DbReadUserRepository implements ReadUserRepositoryInterface
{
    /**
     * @param DatabaseConnection $db
     */
    public function __construct(private readonly DatabaseConnection $db)
    {
    }

    /**
     * @inheritDoc
     */
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
             *  id: int,
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
            ))->setPassword($result['password'])->setId($result['id']);
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function findByToken(string $token): ?User
    {
        $statement = $this->db->prepare(
            <<<'SQL'
            SELECT * from user
                INNER JOIN session ON session.user_id = user.id
                WHERE token = :token
            SQL
        );

        $statement->bindValue(':token', $token, \PDO::PARAM_STR);

        $statement->execute();

        $user = null;

        if (($result = $statement->fetch(\PDO::FETCH_ASSOC)) !== false) {
            /**
             * @var array{
             *  id: int,
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
            ))->setPassword($result['password'])->setId($result['id']);
        }

        return $user;
    }
}
