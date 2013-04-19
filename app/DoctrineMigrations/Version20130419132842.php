<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130419132842 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
                
        $this->addSql("ALTER TABLE art_object ADD textDate VARCHAR(255) NOT NULL");
        $this->addSql("ALTER TABLE art_object ALTER date DROP NOT NULL");
        $this->addSql("ALTER TABLE art_object ALTER description DROP NOT NULL");
        $this->addSql("ALTER TABLE armd_person ADD image_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_person ADD lifeDates VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_person ADD CONSTRAINT FK_A16F8C883DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_A16F8C883DA5256D ON armd_person (image_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");        
        $this->addSql("ALTER TABLE armd_person DROP image_id");
        $this->addSql("ALTER TABLE armd_person DROP lifeDates");
        $this->addSql("ALTER TABLE armd_person DROP CONSTRAINT FK_A16F8C883DA5256D");
        $this->addSql("DROP INDEX IDX_A16F8C883DA5256D");
        $this->addSql("ALTER TABLE art_object DROP textDate");
        $this->addSql("ALTER TABLE art_object ALTER date SET NOT NULL");
        $this->addSql("ALTER TABLE art_object ALTER description SET NOT NULL");
    }
}
