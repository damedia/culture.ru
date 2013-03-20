<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130320122951 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE lecture ALTER view_count SET  DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ALTER view_count DROP NOT NULL");
        $this->addSql("ALTER TABLE lecture ALTER is_top_100_film SET  DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ALTER is_top_100_film DROP NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE lecture ALTER view_count SET  DEFAULT 0");
        $this->addSql("ALTER TABLE lecture ALTER view_count SET NOT NULL");
        $this->addSql("ALTER TABLE lecture ALTER is_top_100_film SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE lecture ALTER is_top_100_film SET NOT NULL");
    }
}
