<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130815133818 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("ALTER TABLE lecture ADD director VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD stars VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE actual_info DROP CONSTRAINT FK_8B8B746B29C1004E");
        $this->addSql("ALTER TABLE actual_info ADD CONSTRAINT FK_8B8B746B29C1004E FOREIGN KEY (video_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE content_perfomance ADD time_length INT DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD stars VARCHAR(255) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("ALTER TABLE lecture DROP director");
        $this->addSql("ALTER TABLE lecture DROP stars");
        $this->addSql("ALTER TABLE actual_info DROP CONSTRAINT fk_8b8b746b29c1004e");
        $this->addSql("ALTER TABLE actual_info ADD CONSTRAINT fk_8b8b746b29c1004e FOREIGN KEY (video_id) REFERENCES tvigle_video (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE content_perfomance DROP time_length");
        $this->addSql("ALTER TABLE content_perfomance DROP stars");
    }
}
