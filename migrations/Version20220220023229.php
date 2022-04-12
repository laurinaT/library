<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220220023229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books ADD loan_date DATETIME DEFAULT NULL, ADD due_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP book_history, CHANGE password password VARCHAR(255) NOT NULL, CHANGE phone_number phone_number VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books DROP loan_date, DROP due_date, CHANGE title title VARCHAR(255) NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE author author TEXT NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE description description VARCHAR(3000) DEFAULT NULL COLLATE `utf8mb4_general_ci`, CHANGE publisher publisher VARCHAR(255) NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE category category TEXT NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE image_name image_name VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user ADD book_history LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(30) DEFAULT NULL');
    }
}
