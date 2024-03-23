<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323213654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE user (
                    id INT AUTO_INCREMENT NOT NULL,
                    username VARCHAR(255) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    roles VARCHAR(255) NOT NULL,
                    PRIMARY KEY(id)
                )
                SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                DROP TABLE user
                SQL
        );
    }
}
