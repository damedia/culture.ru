<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140216015605 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("ALTER TABLE online_translation ADD sidebarImage_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE online_translation ADD CONSTRAINT FK_2BDA8234D5FB5B3D FOREIGN KEY (sidebarImage_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_2BDA8234D5FB5B3D ON online_translation (sidebarImage_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("ALTER TABLE online_translation DROP sidebarImage_id");
        $this->addSql("ALTER TABLE online_translation DROP CONSTRAINT FK_2BDA8234D5FB5B3D");
        $this->addSql("DROP INDEX IDX_2BDA8234D5FB5B3D");
    }
}
