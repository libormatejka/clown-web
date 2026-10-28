<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028143218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE podcast (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, published_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, perex LONGTEXT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_image VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_index VARCHAR(255) DEFAULT NULL, featured TINYINT DEFAULT NULL, status VARCHAR(255) NOT NULL, video_id VARCHAR(255) DEFAULT NULL, thumbnail_id INT DEFAULT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, gallery_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_D7E805BD989D9B62 (slug), INDEX IDX_D7E805BDFDFF2E92 (thumbnail_id), INDEX IDX_D7E805BDA76ED395 (user_id), INDEX IDX_D7E805BD12469DE2 (category_id), INDEX IDX_D7E805BD4E7AF8F (gallery_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE podcast_tag (id INT AUTO_INCREMENT NOT NULL, podcast_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_2921C446786136AB (podcast_id), INDEX IDX_2921C446BAD26311 (tag_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE podcast ADD CONSTRAINT FK_D7E805BDFDFF2E92 FOREIGN KEY (thumbnail_id) REFERENCES media_library (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE podcast ADD CONSTRAINT FK_D7E805BDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE podcast ADD CONSTRAINT FK_D7E805BD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE podcast ADD CONSTRAINT FK_D7E805BD4E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE podcast_tag ADD CONSTRAINT FK_2921C446786136AB FOREIGN KEY (podcast_id) REFERENCES podcast (id)');
        $this->addSql('ALTER TABLE podcast_tag ADD CONSTRAINT FK_2921C446BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE podcast DROP FOREIGN KEY FK_D7E805BDFDFF2E92');
        $this->addSql('ALTER TABLE podcast DROP FOREIGN KEY FK_D7E805BDA76ED395');
        $this->addSql('ALTER TABLE podcast DROP FOREIGN KEY FK_D7E805BD12469DE2');
        $this->addSql('ALTER TABLE podcast DROP FOREIGN KEY FK_D7E805BD4E7AF8F');
        $this->addSql('ALTER TABLE podcast_tag DROP FOREIGN KEY FK_2921C446786136AB');
        $this->addSql('ALTER TABLE podcast_tag DROP FOREIGN KEY FK_2921C446BAD26311');
        $this->addSql('DROP TABLE podcast');
        $this->addSql('DROP TABLE podcast_tag');
    }
}
