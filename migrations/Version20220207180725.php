<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220207180725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE history (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, book_id INT DEFAULT NULL, INDEX IDX_27BA704BA76ED395 (user_id), INDEX IDX_27BA704B16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704B16A2B381 FOREIGN KEY (book_id) REFERENCES books (id)');
        $this->addSql('ALTER TABLE books CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE books ADD CONSTRAINT FK_4A1B2A921BB81215 FOREIGN KEY (last_user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX last_user ON books (last_user)');
        $this->addSql('ALTER TABLE user ADD first_name TEXT NOT NULL, ADD last_name TEXT NOT NULL, ADD phone_number VARCHAR(30) NOT NULL, ADD address VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE history');
        $this->addSql('ALTER TABLE books MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE books DROP FOREIGN KEY FK_4A1B2A921BB81215');
        $this->addSql('ALTER TABLE books DROP PRIMARY KEY');
        $this->addSql('DROP INDEX last_user ON books');
        $this->addSql('ALTER TABLE books CHANGE id id INT NOT NULL, CHANGE title title VARCHAR(255) NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE author author TEXT NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE description description VARCHAR(3000) DEFAULT NULL COLLATE `utf8mb4_general_ci`, CHANGE publisher publisher VARCHAR(255) NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE category category TEXT NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE image_name image_name VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE user DROP first_name, DROP last_name, DROP phone_number, DROP address, CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
