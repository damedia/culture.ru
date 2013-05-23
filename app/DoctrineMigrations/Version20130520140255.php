<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130520140255 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE lecture ADD series_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD CONSTRAINT FK_C16779485278319C FOREIGN KEY (series_id) REFERENCES media__gallery (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_C16779485278319C ON lecture (series_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP INDEX IDX_C16779485278319C");
        $this->addSql("ALTER TABLE lecture DROP CONSTRAINT FK_C16779485278319C");
        $this->addSql("ALTER TABLE lecture DROP series_id");
    }
}
