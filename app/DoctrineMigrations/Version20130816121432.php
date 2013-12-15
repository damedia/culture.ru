<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130816121432 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("ALTER TABLE armd_museum ADD main_page_image_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_museum ADD CONSTRAINT FK_F7F41989F9E91D0 FOREIGN KEY (main_page_image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_F7F41989F9E91D0 ON armd_museum (main_page_image_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("ALTER TABLE armd_museum DROP main_page_image_id");
        $this->addSql("ALTER TABLE armd_museum DROP CONSTRAINT FK_F7F41989F9E91D0");
        $this->addSql("DROP INDEX IDX_F7F41989F9E91D0");
    }
}
