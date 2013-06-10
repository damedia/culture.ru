<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130605183204 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE armd_user_favorites_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE armd_user_favorites (id INT NOT NULL, user_id INT NOT NULL, resourceType VARCHAR(255) NOT NULL, resourceId INT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_C9D2AAA5A76ED395 ON armd_user_favorites (user_id)");
        $this->addSql("ALTER TABLE armd_user_favorites ADD CONSTRAINT FK_C9D2AAA5A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE armd_user_favorites_id_seq CASCADE");
        $this->addSql("DROP TABLE armd_user_favorites");
    }
}
