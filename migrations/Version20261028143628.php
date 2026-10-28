<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028143628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, published_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, perex LONGTEXT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_image VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_index VARCHAR(255) DEFAULT NULL, featured TINYINT DEFAULT NULL, status VARCHAR(255) NOT NULL, source LONGTEXT DEFAULT NULL, thumbnail_id INT DEFAULT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, gallery_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_5A8A6C8D989D9B62 (slug), INDEX IDX_5A8A6C8DFDFF2E92 (thumbnail_id), INDEX IDX_5A8A6C8DA76ED395 (user_id), INDEX IDX_5A8A6C8D12469DE2 (category_id), INDEX IDX_5A8A6C8D4E7AF8F (gallery_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post_revision (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, perex LONGTEXT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_image VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_index VARCHAR(255) DEFAULT NULL, featured TINYINT DEFAULT NULL, initial TINYINT DEFAULT NULL, status VARCHAR(255) NOT NULL, source LONGTEXT DEFAULT NULL, thumbnail_id INT DEFAULT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, gallery_id INT DEFAULT NULL, post_id INT DEFAULT NULL, INDEX IDX_D28E9C6DFDFF2E92 (thumbnail_id), INDEX IDX_D28E9C6DA76ED395 (user_id), INDEX IDX_D28E9C6D12469DE2 (category_id), INDEX IDX_D28E9C6D4E7AF8F (gallery_id), INDEX IDX_D28E9C6D4B89032C (post_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post_tag (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_5ACE3AF04B89032C (post_id), INDEX IDX_5ACE3AF0BAD26311 (tag_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DFDFF2E92 FOREIGN KEY (thumbnail_id) REFERENCES media_library (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D4E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post_revision ADD CONSTRAINT FK_D28E9C6DFDFF2E92 FOREIGN KEY (thumbnail_id) REFERENCES media_library (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post_revision ADD CONSTRAINT FK_D28E9C6DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post_revision ADD CONSTRAINT FK_D28E9C6D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post_revision ADD CONSTRAINT FK_D28E9C6D4E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post_revision ADD CONSTRAINT FK_D28E9C6D4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF04B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF0BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DFDFF2E92');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D12469DE2');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D4E7AF8F');
        $this->addSql('ALTER TABLE post_revision DROP FOREIGN KEY FK_D28E9C6DFDFF2E92');
        $this->addSql('ALTER TABLE post_revision DROP FOREIGN KEY FK_D28E9C6DA76ED395');
        $this->addSql('ALTER TABLE post_revision DROP FOREIGN KEY FK_D28E9C6D12469DE2');
        $this->addSql('ALTER TABLE post_revision DROP FOREIGN KEY FK_D28E9C6D4E7AF8F');
        $this->addSql('ALTER TABLE post_revision DROP FOREIGN KEY FK_D28E9C6D4B89032C');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF04B89032C');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF0BAD26311');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_revision');
        $this->addSql('DROP TABLE post_tag');
    }
}
