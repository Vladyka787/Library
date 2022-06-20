<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220619225607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD book_author VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE book DROP "book_autрhor"');
        $this->addSql('ALTER TABLE book RENAME COLUMN �Иbook_read_date TO book_date_read');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
//        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE book ADD "book_autрhor" VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE book DROP book_author');
        $this->addSql('ALTER TABLE book RENAME COLUMN book_date_read TO "�Иbook_read_date"');
    }
}
