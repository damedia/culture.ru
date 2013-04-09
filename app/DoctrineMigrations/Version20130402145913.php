<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130402145913 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE content_perfomance_ganre_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE content_perfomance_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE content_perfomance_ganre (id INT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE TABLE content_perfomance (id INT NOT NULL, perfomance_video_id INT DEFAULT NULL, trailer_video_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, published BOOLEAN DEFAULT NULL, description TEXT DEFAULT NULL, view_count INT DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_B6C6A5495BC8D075 ON content_perfomance (perfomance_video_id)");
        $this->addSql("CREATE INDEX IDX_B6C6A549CEED7EC9 ON content_perfomance (trailer_video_id)");
        $this->addSql("CREATE TABLE content_perfomance_perfomance_ganre (perfomance_id INT NOT NULL, perfomanceganre_id INT NOT NULL, PRIMARY KEY(perfomance_id, perfomanceganre_id))");
        $this->addSql("CREATE INDEX IDX_EE18212DE3A77AF1 ON content_perfomance_perfomance_ganre (perfomance_id)");
        $this->addSql("CREATE INDEX IDX_EE18212DCC26A8B7 ON content_perfomance_perfomance_ganre (perfomanceganre_id)");
        $this->addSql("ALTER TABLE content_perfomance ADD CONSTRAINT FK_B6C6A5495BC8D075 FOREIGN KEY (perfomance_video_id) REFERENCES tvigle_video (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE content_perfomance ADD CONSTRAINT FK_B6C6A549CEED7EC9 FOREIGN KEY (trailer_video_id) REFERENCES tvigle_video (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE content_perfomance_perfomance_ganre ADD CONSTRAINT FK_EE18212DE3A77AF1 FOREIGN KEY (perfomance_id) REFERENCES content_perfomance (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE content_perfomance_perfomance_ganre ADD CONSTRAINT FK_EE18212DCC26A8B7 FOREIGN KEY (perfomanceganre_id) REFERENCES content_perfomance_ganre (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE content_perfomance_perfomance_ganre DROP CONSTRAINT FK_EE18212DCC26A8B7");
        $this->addSql("ALTER TABLE content_perfomance_perfomance_ganre DROP CONSTRAINT FK_EE18212DE3A77AF1");
        $this->addSql("DROP SEQUENCE content_perfomance_ganre_id_seq");
        $this->addSql("DROP SEQUENCE content_perfomance_id_seq");
        $this->addSql("DROP TABLE content_perfomance_ganre");
        $this->addSql("DROP TABLE content_perfomance");
        $this->addSql("DROP TABLE content_perfomance_perfomance_ganre");
    }
}
