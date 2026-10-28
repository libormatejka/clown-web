<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028132830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gallery (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, perex VARCHAR(255) DEFAULT NULL, content VARCHAR(255) DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_index VARCHAR(255) DEFAULT NULL, meta_image VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, published_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, category_id INT DEFAULT NULL, thumbnail_id INT DEFAULT NULL, INDEX IDX_472B783A12469DE2 (category_id), INDEX IDX_472B783AFDFF2E92 (thumbnail_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE gallery_media_library (id INT AUTO_INCREMENT NOT NULL, gallery_id INT NOT NULL, media_library_id INT NOT NULL, INDEX IDX_60C11C874E7AF8F (gallery_id), INDEX IDX_60C11C87F4008F43 (media_library_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE gallery_tag (id INT AUTO_INCREMENT NOT NULL, gallery_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_155EA60E4E7AF8F (gallery_id), INDEX IDX_155EA60EBAD26311 (tag_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783AFDFF2E92 FOREIGN KEY (thumbnail_id) REFERENCES media_library (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE gallery_media_library ADD CONSTRAINT FK_60C11C874E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id)');
        $this->addSql('ALTER TABLE gallery_media_library ADD CONSTRAINT FK_60C11C87F4008F43 FOREIGN KEY (media_library_id) REFERENCES media_library (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gallery_tag ADD CONSTRAINT FK_155EA60E4E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id)');
        $this->addSql('ALTER TABLE gallery_tag ADD CONSTRAINT FK_155EA60EBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783A12469DE2');
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783AFDFF2E92');
        $this->addSql('ALTER TABLE gallery_media_library DROP FOREIGN KEY FK_60C11C874E7AF8F');
        $this->addSql('ALTER TABLE gallery_media_library DROP FOREIGN KEY FK_60C11C87F4008F43');
        $this->addSql('ALTER TABLE gallery_tag DROP FOREIGN KEY FK_155EA60E4E7AF8F');
        $this->addSql('ALTER TABLE gallery_tag DROP FOREIGN KEY FK_155EA60EBAD26311');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE gallery_media_library');
        $this->addSql('DROP TABLE gallery_tag');
    }
}
