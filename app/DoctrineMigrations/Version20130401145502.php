<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130401145502 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE content_news ADD countryDistrict_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE content_news ADD CONSTRAINT FK_D0B17495185F5F80 FOREIGN KEY (countryDistrict_id) REFERENCES address_country_district (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_D0B17495185F5F80 ON content_news (countryDistrict_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE content_news DROP CONSTRAINT FK_D0B17495185F5F80");
        $this->addSql("ALTER TABLE content_news DROP countryDistrict_id");
    }
}
