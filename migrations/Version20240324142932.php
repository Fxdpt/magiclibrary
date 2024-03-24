<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324142932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
       $this->addSql(
            <<<SQL
                CREATE TABLE `session` (
                    id INT AUTO_INCREMENT NOT NULL,
                    token VARCHAR(255) NOT NULL,
                    user_id INT NOT NULL,
                    expiration_date INT NOT NULL,
                    PRIMARY KEY(id),
                    CONSTRAINT fk_user_id
                        FOREIGN KEY(user_id)
                            REFERENCES `user`(id)
                    ON DELETE CASCADE
                )
                SQL
       );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
                DROP TABLE `session`;
                SQL
        );
    }
}
