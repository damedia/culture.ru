<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130320191153 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE atlas_object ADD show_on_main BOOLEAN DEFAULT 'false' NOT NULL");
        $this->addSql("ALTER TABLE atlas_object ADD show_on_main_ord INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE atlas_object DROP show_at_homepage");
        $this->addSql("ALTER TABLE content_news ADD show_on_main BOOLEAN DEFAULT 'false' NOT NULL");
        $this->addSql("ALTER TABLE content_news ADD show_on_main_ord INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE content_news ALTER is_on_map SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE armd_museum ADD show_on_main BOOLEAN DEFAULT 'false' NOT NULL");
        $this->addSql("ALTER TABLE armd_museum ADD show_on_main_ord INT DEFAULT 0 NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE atlas_object ADD show_at_homepage BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE atlas_object DROP show_on_main");
        $this->addSql("ALTER TABLE atlas_object DROP show_on_main_ord");
        $this->addSql("ALTER TABLE armd_museum DROP show_on_main");
        $this->addSql("ALTER TABLE armd_museum DROP show_on_main_ord");
        $this->addSql("ALTER TABLE content_news DROP show_on_main");
        $this->addSql("ALTER TABLE content_news DROP show_on_main_ord");
    }
}
