<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130412152624 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE armd_museum_guide_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE armd_museum_guide (id INT NOT NULL, image_id INT DEFAULT NULL, file_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, announce TEXT DEFAULT NULL, body TEXT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_AD667FEF3DA5256D ON armd_museum_guide (image_id)");
        $this->addSql("CREATE INDEX IDX_AD667FEF93CB796C ON armd_museum_guide (file_id)");
        $this->addSql("ALTER TABLE armd_museum_guide ADD CONSTRAINT FK_AD667FEF3DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_museum_guide ADD CONSTRAINT FK_AD667FEF93CB796C FOREIGN KEY (file_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE armd_museum_guide_id_seq");
        $this->addSql("DROP TABLE armd_museum_guide");
    }
}
