<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130423150925 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE banner ALTER COLUMN image_path DROP NOT NULL");
        $this->addSql("ALTER TABLE banner ADD image_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE banner ADD CONSTRAINT FK_6F9DB8E73DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_6F9DB8E73DA5256D ON banner (image_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE banner ALTER COLUMN image_path SET NOT NULL");
        $this->addSql("ALTER TABLE banner DROP image_id");
        $this->addSql("ALTER TABLE banner DROP CONSTRAINT FK_6F9DB8E73DA5256D");
        $this->addSql("DROP INDEX IDX_6F9DB8E73DA5256D");
    }
}
