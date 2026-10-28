<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028144447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE api_key (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, key_hash VARCHAR(255) NOT NULL, published_at DATETIME DEFAULT NULL, expires_at DATETIME DEFAULT NULL, scopes LONGTEXT DEFAULT NULL, is_revoked TINYINT DEFAULT NULL, status TINYINT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE api_log (id INT AUTO_INCREMENT NOT NULL, published_at DATETIME DEFAULT NULL, ip LONGTEXT DEFAULT NULL, method LONGTEXT DEFAULT NULL, user_agent LONGTEXT DEFAULT NULL, path LONGTEXT DEFAULT NULL, status_code INT DEFAULT NULL, status_message LONGTEXT DEFAULT NULL, api_key_id INT DEFAULT NULL, INDEX IDX_CCBD2EF18BE312B3 (api_key_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE api_log ADD CONSTRAINT FK_CCBD2EF18BE312B3 FOREIGN KEY (api_key_id) REFERENCES api_key (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_log DROP FOREIGN KEY FK_CCBD2EF18BE312B3');
        $this->addSql('DROP TABLE api_key');
        $this->addSql('DROP TABLE api_log');
    }
}
