<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130730170809 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE damedia_project_block_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE damedia_project_chunk_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE damedia_project_page_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE damedia_project_template_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE damedia_project_block (id INT NOT NULL, page INT DEFAULT NULL, placeholder VARCHAR(255) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_D4960B30140AB620 ON damedia_project_block (page)");
        $this->addSql("CREATE TABLE damedia_project_chunk (id INT NOT NULL, block INT DEFAULT NULL, content TEXT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_C28BED3C831B9722 ON damedia_project_chunk (block)");
        $this->addSql("CREATE TABLE damedia_project_page (id INT NOT NULL, template INT DEFAULT NULL, parent INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_published BOOLEAN NOT NULL, stylesheet TEXT DEFAULT NULL, javascript TEXT DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_F123815497601F83 ON damedia_project_page (template)");
        $this->addSql("CREATE INDEX IDX_F12381543D8E604F ON damedia_project_page (parent)");
        $this->addSql("CREATE TABLE damedia_project_template (id INT NOT NULL, title VARCHAR(255) NOT NULL, twig_file_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("ALTER TABLE damedia_project_block ADD CONSTRAINT FK_D4960B30140AB620 FOREIGN KEY (page) REFERENCES damedia_project_page (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE damedia_project_chunk ADD CONSTRAINT FK_C28BED3C831B9722 FOREIGN KEY (block) REFERENCES damedia_project_block (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE damedia_project_page ADD CONSTRAINT FK_F123815497601F83 FOREIGN KEY (template) REFERENCES damedia_project_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE damedia_project_page ADD CONSTRAINT FK_F12381543D8E604F FOREIGN KEY (parent) REFERENCES damedia_project_page (id) NOT DEFERRABLE INITIALLY IMMEDIATE");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE damedia_project_chunk DROP CONSTRAINT FK_C28BED3C831B9722");
        $this->addSql("ALTER TABLE damedia_project_block DROP CONSTRAINT FK_D4960B30140AB620");
        $this->addSql("ALTER TABLE damedia_project_page DROP CONSTRAINT FK_F12381543D8E604F");
        $this->addSql("ALTER TABLE damedia_project_page DROP CONSTRAINT FK_F123815497601F83");
        $this->addSql("DROP SEQUENCE damedia_project_block_id_seq CASCADE");
        $this->addSql("DROP SEQUENCE damedia_project_chunk_id_seq CASCADE");
        $this->addSql("DROP SEQUENCE damedia_project_page_id_seq CASCADE");
        $this->addSql("DROP SEQUENCE damedia_project_template_id_seq CASCADE");
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("DROP TABLE damedia_project_block");
        $this->addSql("DROP TABLE damedia_project_chunk");
        $this->addSql("DROP TABLE damedia_project_page");
        $this->addSql("DROP TABLE damedia_project_template");

    }
}
