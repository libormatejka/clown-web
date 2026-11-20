<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Tabulky auditního logu (Clown\Core\Entity\Log, LogType) — viz EntityLogSubscriber.
 */
final class Version20261120120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Vytvoření tabulek log a log_type pro auditní log';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE log_type (id INT AUTO_INCREMENT NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, entity VARCHAR(255) NOT NULL, entity_id INT DEFAULT NULL, entity_type VARCHAR(255) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, ip_address VARCHAR(255) DEFAULT NULL, published_at DATETIME DEFAULT NULL, status INT NOT NULL, user_id INT DEFAULT NULL, log_type_id INT NOT NULL, INDEX IDX_8F3F68C5A76ED395 (user_id), INDEX IDX_8F3F68C5FD81B404 (log_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5FD81B404 FOREIGN KEY (log_type_id) REFERENCES log_type (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C5A76ED395');
        $this->addSql('ALTER TABLE log DROP FOREIGN KEY FK_8F3F68C5FD81B404');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE log_type');
    }
}
