<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261027132202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_library (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, meta_data LONGTEXT DEFAULT NULL, category_id INT DEFAULT NULL, copyright VARCHAR(255) DEFAULT NULL, file_type VARCHAR(255) DEFAULT NULL, published_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_item (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, position INT DEFAULT NULL, status VARCHAR(255) NOT NULL, target VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, parent_id INT DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, menu_id INT NOT NULL, INDEX IDX_D754D550727ACA70 (parent_id), INDEX IDX_D754D550CCD7E912 (menu_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, perex LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_image VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_index VARCHAR(255) DEFAULT NULL, published_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, status VARCHAR(255) NOT NULL, layout VARCHAR(255) DEFAULT NULL, thumbnail_id INT DEFAULT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_140AB620989D9B62 (slug), INDEX IDX_140AB620FDFF2E92 (thumbnail_id), INDEX IDX_140AB620A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, status INT NOT NULL, google_id VARCHAR(255) DEFAULT NULL, google_login_enabled TINYINT DEFAULT 1 NOT NULL, two_factor_secret VARCHAR(255) DEFAULT NULL, two_factor_enabled TINYINT DEFAULT 0 NOT NULL, two_factor_allowed TINYINT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_8D93D64976F5C865 (google_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D550727ACA70 FOREIGN KEY (parent_id) REFERENCES menu_item (id)');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D550CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620FDFF2E92 FOREIGN KEY (thumbnail_id) REFERENCES media_library (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D550727ACA70');
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D550CCD7E912');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620FDFF2E92');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620A76ED395');
        $this->addSql('DROP TABLE media_library');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_item');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE user');
    }
}
