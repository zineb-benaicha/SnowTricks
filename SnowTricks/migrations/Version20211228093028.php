<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211228093028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE figure (id INT AUTO_INCREMENT NOT NULL, groupe_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(350) NOT NULL, creation_date DATETIME NOT NULL, last_update_date DATETIME NOT NULL, INDEX IDX_2F57B37A7A45358C (groupe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, figure_id INT NOT NULL, type VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, is_principal TINYINT(1) NOT NULL, INDEX IDX_6A2CA10C5C011B5 (figure_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, figure_id INT NOT NULL, content VARCHAR(500) NOT NULL, creation_date DATETIME NOT NULL, INDEX IDX_B6BD307FA76ED395 (user_id), INDEX IDX_B6BD307F5C011B5 (figure_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(500) NOT NULL, avatar VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37A7A45358C FOREIGN KEY (groupe_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C5C011B5');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F5C011B5');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37A7A45358C');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA76ED395');
        $this->addSql('DROP TABLE figure');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE user');
    }
}
