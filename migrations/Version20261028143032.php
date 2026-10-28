<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028143032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE portfolio (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, perex LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, published_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_image VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_index VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, thumbnail_id INT DEFAULT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_A9ED1062989D9B62 (slug), INDEX IDX_A9ED1062FDFF2E92 (thumbnail_id), INDEX IDX_A9ED1062A76ED395 (user_id), INDEX IDX_A9ED106212469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE portfolio_tag (id INT AUTO_INCREMENT NOT NULL, portfolio_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_8731A46CB96B5643 (portfolio_id), INDEX IDX_8731A46CBAD26311 (tag_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE portfolio ADD CONSTRAINT FK_A9ED1062FDFF2E92 FOREIGN KEY (thumbnail_id) REFERENCES media_library (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE portfolio ADD CONSTRAINT FK_A9ED1062A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE portfolio ADD CONSTRAINT FK_A9ED106212469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE portfolio_tag ADD CONSTRAINT FK_8731A46CB96B5643 FOREIGN KEY (portfolio_id) REFERENCES portfolio (id)');
        $this->addSql('ALTER TABLE portfolio_tag ADD CONSTRAINT FK_8731A46CBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE portfolio DROP FOREIGN KEY FK_A9ED1062FDFF2E92');
        $this->addSql('ALTER TABLE portfolio DROP FOREIGN KEY FK_A9ED1062A76ED395');
        $this->addSql('ALTER TABLE portfolio DROP FOREIGN KEY FK_A9ED106212469DE2');
        $this->addSql('ALTER TABLE portfolio_tag DROP FOREIGN KEY FK_8731A46CB96B5643');
        $this->addSql('ALTER TABLE portfolio_tag DROP FOREIGN KEY FK_8731A46CBAD26311');
        $this->addSql('DROP TABLE portfolio');
        $this->addSql('DROP TABLE portfolio_tag');
    }
}
