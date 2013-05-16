<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130430135230 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE tourist_route ADD primary_image_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE tourist_route ADD CONSTRAINT FK_A130300F1CDA489C FOREIGN KEY (primary_image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_A130300F1CDA489C ON tourist_route (primary_image_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP INDEX IDX_A130300F1CDA489C");
        $this->addSql("ALTER TABLE tourist_route DROP CONSTRAINT FK_A130300F1CDA489C");
        $this->addSql("ALTER TABLE tourist_route DROP primary_image_id");
    }
}
