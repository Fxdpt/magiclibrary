<?php

namespace MagicLibrary\Security\Authentication\Infrastructure\Repository;

use MagicLibrary\Common\DatabaseConnection;
use MagicLibrary\Security\Authentication\Application\Repository\WriteSessionRepositoryInterface;
use MagicLibrary\Security\Authentication\Domain\Model\Session;
use MagicLibrary\Security\Authentication\Domain\Model\User;

final class DbWriteSessionRepository implements WriteSessionRepositoryInterface
{
    public function __construct(private readonly DatabaseConnection $db)
    {
    }

    /**
     * @inheritDoc
     */
    public function add(Session $session): void
    {
        $statement = $this->db->prepare(
            <<<SQL
                INSERT INTO `session` (user_id,token,expiration_date)
                VALUES (:userId, :token, :expirationDate)
                SQL
        );

        $statement->bindValue(':userId', $session->getUserId(), \PDO::PARAM_INT);
        $statement->bindValue(':token', $session->getToken(), \PDO::PARAM_STR);
        $statement->bindValue(':expirationDate', $session->getExpireAt()->getTimestamp(), \PDO::PARAM_INT);

        $statement->execute();
    }

    /**
     * @inheritDoc
     */
    public function refresh(User $user): void
    {
        $statement = $this->db->prepare(
            <<<SQL
                UPDATE `session` SET expiration_date = :expirationDate WHERE user_id = :userId
                SQL
        );

        $statement->bindValue(
            ':expirationDate',
            (new \DateTimeImmutable())->add(new \DateInterval('PT2H'))->getTimestamp(),
            \PDO::PARAM_INT
        );
        $statement->bindValue(':userId', $user->getId(), \PDO::PARAM_INT);

        $statement->execute();
    }

    /**
     * @inheritDoc
     */
    public function delete(User $user): void
    {
        $statement = $this->db->prepare(
            <<<SQL
                DELETE FROM `session` WHERE user_id = :userId
                SQL
        );
        $statement->bindValue(':userId', $user->getId(), \PDO::PARAM_INT);

        $statement->execute();
    }

    /**
     * @inheritDoc
     */
    public function deleteExpiredTokens(): void
    {
        $statement = $this->db->prepare(
            <<<SQL
                DELETE FROM `session` WHERE expiration_date < :now
                SQL
        );

        $statement->bindValue(':now', (new \DateTimeImmutable())->getTimestamp(), \PDO::PARAM_INT);

        $statement->execute();
    }
}
