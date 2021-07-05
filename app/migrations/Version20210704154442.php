<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210704154442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (sku VARCHAR(128) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(sku)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchased (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, product_sku VARCHAR(128) DEFAULT NULL, INDEX IDX_29B7B439A76ED395 (user_id), INDEX IDX_29B7B439EFBF6CDB (product_sku), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(128) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchased ADD CONSTRAINT FK_29B7B439A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE purchased ADD CONSTRAINT FK_29B7B439EFBF6CDB FOREIGN KEY (product_sku) REFERENCES product (sku)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchased DROP FOREIGN KEY FK_29B7B439EFBF6CDB');
        $this->addSql('ALTER TABLE purchased DROP FOREIGN KEY FK_29B7B439A76ED395');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE purchased');
        $this->addSql('DROP TABLE user');
    }
}
