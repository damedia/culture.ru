<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130416110407 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE armd_museum_guide ADD city_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_museum_guide ADD museum_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_museum_guide DROP city");
        $this->addSql("ALTER TABLE armd_museum_guide ADD CONSTRAINT FK_AD667FEF8BAC62AF FOREIGN KEY (city_id) REFERENCES address_city (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_museum_guide ADD CONSTRAINT FK_AD667FEF4B52E5B5 FOREIGN KEY (museum_id) REFERENCES armd_real_museum (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_AD667FEF8BAC62AF ON armd_museum_guide (city_id)");
        $this->addSql("CREATE INDEX IDX_AD667FEF4B52E5B5 ON armd_museum_guide (museum_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE armd_museum_guide ADD city VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_museum_guide DROP city_id");
        $this->addSql("ALTER TABLE armd_museum_guide DROP museum_id");
        $this->addSql("ALTER TABLE armd_museum_guide DROP CONSTRAINT FK_AD667FEF8BAC62AF");
        $this->addSql("ALTER TABLE armd_museum_guide DROP CONSTRAINT FK_AD667FEF4B52E5B5");
        $this->addSql("DROP INDEX IDX_AD667FEF8BAC62AF");
        $this->addSql("DROP INDEX IDX_AD667FEF4B52E5B5");
    }
}
