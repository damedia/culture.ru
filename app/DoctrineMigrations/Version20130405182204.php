<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130405182204 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE poll_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE poll_answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE poll_reveal_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE poll_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE poll_vote_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE poll (id INT NOT NULL, poll_type_id INT NOT NULL, poll_reveal_type_id INT NOT NULL, description TEXT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_84BCFA45391901AC ON poll (poll_type_id)");
        $this->addSql("CREATE INDEX IDX_84BCFA45B380ED6C ON poll (poll_reveal_type_id)");
        $this->addSql("CREATE TABLE poll_answer (id INT NOT NULL, poll_id INT NOT NULL, answer_text VARCHAR(255) NOT NULL, sort_index INT NOT NULL, vote_count INT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_36D8097E3C947C0F ON poll_answer (poll_id)");
        $this->addSql("CREATE TABLE poll_reveal_type (id INT NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE TABLE poll_type (id INT NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE TABLE poll_vote (id INT NOT NULL, poll_id INT NOT NULL, answer_text VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_ED568EBE3C947C0F ON poll_vote (poll_id)");
        $this->addSql("ALTER TABLE poll ADD CONSTRAINT FK_84BCFA45391901AC FOREIGN KEY (poll_type_id) REFERENCES poll_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE poll ADD CONSTRAINT FK_84BCFA45B380ED6C FOREIGN KEY (poll_reveal_type_id) REFERENCES poll_reveal_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE poll_answer ADD CONSTRAINT FK_36D8097E3C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE poll_vote ADD CONSTRAINT FK_ED568EBE3C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE poll_answer DROP CONSTRAINT FK_36D8097E3C947C0F");
        $this->addSql("ALTER TABLE poll_vote DROP CONSTRAINT FK_ED568EBE3C947C0F");
        $this->addSql("ALTER TABLE poll DROP CONSTRAINT FK_84BCFA45B380ED6C");
        $this->addSql("ALTER TABLE poll DROP CONSTRAINT FK_84BCFA45391901AC");
        $this->addSql("DROP SEQUENCE poll_id_seq");
        $this->addSql("DROP SEQUENCE poll_answer_id_seq");
        $this->addSql("DROP SEQUENCE poll_reveal_type_id_seq");
        $this->addSql("DROP SEQUENCE poll_type_id_seq");
        $this->addSql("DROP SEQUENCE poll_vote_id_seq");
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("DROP TABLE poll");
        $this->addSql("DROP TABLE poll_answer");
        $this->addSql("DROP TABLE poll_reveal_type");
        $this->addSql("DROP TABLE poll_type");
        $this->addSql("DROP TABLE poll_vote");
    }
}
