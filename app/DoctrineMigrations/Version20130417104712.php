<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130417104712 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE armd_real_museum ADD email VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_real_museum ADD phone VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_real_museum ADD schedule TEXT DEFAULT NULL");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE armd_real_museum DROP email");
        $this->addSql("ALTER TABLE armd_real_museum DROP phone");
        $this->addSql("ALTER TABLE armd_real_museum DROP schedule");

    }
}
