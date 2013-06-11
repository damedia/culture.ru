<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130606143747 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE armd_war_gallery_member_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE armd_war_gallery_member (id INT NOT NULL, preview_id INT DEFAULT NULL, image_id INT DEFAULT NULL, published BOOLEAN DEFAULT NULL, name TEXT NOT NULL, years TEXT DEFAULT NULL, ranks TEXT DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_C1408986CDE46FDB ON armd_war_gallery_member (preview_id)");
        $this->addSql("CREATE INDEX IDX_C14089863DA5256D ON armd_war_gallery_member (image_id)");
        $this->addSql("ALTER TABLE armd_war_gallery_member ADD CONSTRAINT FK_C1408986CDE46FDB FOREIGN KEY (preview_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_war_gallery_member ADD CONSTRAINT FK_C14089863DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE armd_war_gallery_member_id_seq CASCADE");
        $this->addSql("DROP TABLE armd_war_gallery_member");
    }
}
