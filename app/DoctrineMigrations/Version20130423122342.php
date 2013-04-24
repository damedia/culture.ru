<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130423122342 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
               
        $this->addSql("ALTER TABLE art_object ADD virtual_tour_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE art_object ADD virtualTourUrl VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE art_object ADD CONSTRAINT FK_E712E010239A5E8C FOREIGN KEY (virtual_tour_id) REFERENCES armd_museum (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_E712E010239A5E8C ON art_object (virtual_tour_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
                
        $this->addSql("ALTER TABLE art_object DROP virtual_tour_id");
        $this->addSql("ALTER TABLE art_object DROP virtualTourUrl");
        $this->addSql("ALTER TABLE art_object DROP CONSTRAINT FK_E712E010239A5E8C");
        $this->addSql("DROP INDEX IDX_E712E010239A5E8C");       
    }
}
