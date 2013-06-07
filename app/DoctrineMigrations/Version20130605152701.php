<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130605152701 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE comment_notice_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE comment_notice (id INT NOT NULL, comment_id INT DEFAULT NULL, user_id INT DEFAULT NULL, type INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_821E1DD0F8697D13 ON comment_notice (comment_id)");
        $this->addSql("CREATE INDEX IDX_821E1DD0A76ED395 ON comment_notice (user_id)");
        $this->addSql("ALTER TABLE comment_notice ADD CONSTRAINT FK_821E1DD0F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE comment_notice ADD CONSTRAINT FK_821E1DD0A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE fos_user_user ADD notice_on_comment INT DEFAULT NULL");
        $this->addSql("UPDATE fos_user_user SET notice_on_comment = 0 WHERE notice_on_comment IS NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE comment_notice_id_seq CASCADE");
        $this->addSql("DROP TABLE comment_notice");
        $this->addSql("ALTER TABLE fos_user_user DROP notice_on_comment");
    }
}
