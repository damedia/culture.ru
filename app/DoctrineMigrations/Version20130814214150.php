<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130814214150 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE damedia_project_page ADD banner_image_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE damedia_project_page ADD CONSTRAINT FK_F12381543F9CEB4E FOREIGN KEY (banner_image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_F12381543F9CEB4E ON damedia_project_page (banner_image_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE damedia_project_page DROP banner_image_id");
        $this->addSql("ALTER TABLE damedia_project_page DROP CONSTRAINT FK_F12381543F9CEB4E");
        $this->addSql("DROP INDEX IDX_F12381543F9CEB4E");
    }
}
