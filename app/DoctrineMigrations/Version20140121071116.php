<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140121071116 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("ALTER TABLE armd_theater ADD thread_id VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_theater ADD CONSTRAINT FK_1C4DED12E2904019 FOREIGN KEY (thread_id) REFERENCES comment_thread (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_1C4DED12E2904019 ON armd_theater (thread_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("ALTER TABLE armd_theater DROP thread_id");
        $this->addSql("ALTER TABLE armd_theater DROP CONSTRAINT FK_1C4DED12E2904019");
        $this->addSql("DROP INDEX IDX_1C4DED12E2904019");
    }
}
