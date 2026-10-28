<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028143947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, published_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, perex LONGTEXT DEFAULT NULL, review_text LONGTEXT DEFAULT NULL, review_number INT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_image VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_index VARCHAR(255) DEFAULT NULL, featured TINYINT DEFAULT NULL, status VARCHAR(255) NOT NULL, thumbnail_id INT DEFAULT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, gallery_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_794381C6989D9B62 (slug), INDEX IDX_794381C6FDFF2E92 (thumbnail_id), INDEX IDX_794381C6A76ED395 (user_id), INDEX IDX_794381C612469DE2 (category_id), INDEX IDX_794381C64E7AF8F (gallery_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE review_revision (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, published_at DATETIME DEFAULT NULL, perex LONGTEXT DEFAULT NULL, review_text LONGTEXT DEFAULT NULL, review_number INT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_image VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_index VARCHAR(255) DEFAULT NULL, featured TINYINT DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, initial TINYINT DEFAULT NULL, thumbnail_id INT DEFAULT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, gallery_id INT DEFAULT NULL, review_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_4E219CC7989D9B62 (slug), INDEX IDX_4E219CC7FDFF2E92 (thumbnail_id), INDEX IDX_4E219CC7A76ED395 (user_id), INDEX IDX_4E219CC712469DE2 (category_id), INDEX IDX_4E219CC74E7AF8F (gallery_id), INDEX IDX_4E219CC73E2E969B (review_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE review_tag (id INT AUTO_INCREMENT NOT NULL, review_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_FBEBB4A3E2E969B (review_id), INDEX IDX_FBEBB4ABAD26311 (tag_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6FDFF2E92 FOREIGN KEY (thumbnail_id) REFERENCES media_library (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C64E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE review_revision ADD CONSTRAINT FK_4E219CC7FDFF2E92 FOREIGN KEY (thumbnail_id) REFERENCES media_library (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE review_revision ADD CONSTRAINT FK_4E219CC7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE review_revision ADD CONSTRAINT FK_4E219CC712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE review_revision ADD CONSTRAINT FK_4E219CC74E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE review_revision ADD CONSTRAINT FK_4E219CC73E2E969B FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review_tag ADD CONSTRAINT FK_FBEBB4A3E2E969B FOREIGN KEY (review_id) REFERENCES review (id)');
        $this->addSql('ALTER TABLE review_tag ADD CONSTRAINT FK_FBEBB4ABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6FDFF2E92');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C612469DE2');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C64E7AF8F');
        $this->addSql('ALTER TABLE review_revision DROP FOREIGN KEY FK_4E219CC7FDFF2E92');
        $this->addSql('ALTER TABLE review_revision DROP FOREIGN KEY FK_4E219CC7A76ED395');
        $this->addSql('ALTER TABLE review_revision DROP FOREIGN KEY FK_4E219CC712469DE2');
        $this->addSql('ALTER TABLE review_revision DROP FOREIGN KEY FK_4E219CC74E7AF8F');
        $this->addSql('ALTER TABLE review_revision DROP FOREIGN KEY FK_4E219CC73E2E969B');
        $this->addSql('ALTER TABLE review_tag DROP FOREIGN KEY FK_FBEBB4A3E2E969B');
        $this->addSql('ALTER TABLE review_tag DROP FOREIGN KEY FK_FBEBB4ABAD26311');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE review_revision');
        $this->addSql('DROP TABLE review_tag');
    }
}
