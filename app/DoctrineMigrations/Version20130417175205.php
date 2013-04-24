<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130417175205 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE content_perfomance_review_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE content_perfomance_review (id INT NOT NULL, perfomance_id INT DEFAULT NULL, author_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, published BOOLEAN DEFAULT NULL, body TEXT DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_467EDA8FE3A77AF1 ON content_perfomance_review (perfomance_id)");
        $this->addSql("CREATE INDEX IDX_467EDA8FF675F31B ON content_perfomance_review (author_id)");
        $this->addSql("ALTER TABLE content_perfomance_review ADD CONSTRAINT FK_467EDA8FE3A77AF1 FOREIGN KEY (perfomance_id) REFERENCES content_perfomance (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE content_perfomance_review ADD CONSTRAINT FK_467EDA8FF675F31B FOREIGN KEY (author_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE content_perfomance_review_id_seq");
        $this->addSql("DROP TABLE content_perfomance_review");

    }
}
