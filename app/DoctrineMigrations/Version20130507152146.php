<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130507152146 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE lecture DROP CONSTRAINT fk_c1677948f308dfc7");
        $this->addSql("DROP SEQUENCE lecture_type_id_seq");
        $this->addSql("CREATE SEQUENCE lecture_genre_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE lecture_genre_lecture (lecture_id INT NOT NULL, lecturegenre_id INT NOT NULL, PRIMARY KEY(lecture_id, lecturegenre_id))");
        $this->addSql("CREATE INDEX IDX_26DE430235E32FCD ON lecture_genre_lecture (lecture_id)");
        $this->addSql("CREATE INDEX IDX_26DE43029A6136 ON lecture_genre_lecture (lecturegenre_id)");
        $this->addSql("CREATE TABLE lecture_genre (id INT NOT NULL, lecture_super_type_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, level INT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_283DE616AB00CD8D ON lecture_genre (lecture_super_type_id)");
        $this->addSql("ALTER TABLE lecture_genre_lecture ADD CONSTRAINT FK_26DE430235E32FCD FOREIGN KEY (lecture_id) REFERENCES lecture (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE lecture_genre_lecture ADD CONSTRAINT FK_26DE43029A6136 FOREIGN KEY (lecturegenre_id) REFERENCES lecture_genre (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE lecture_genre ADD CONSTRAINT FK_283DE616AB00CD8D FOREIGN KEY (lecture_super_type_id) REFERENCES lecture_super_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("DROP TABLE lecture_type");
        $this->addSql("ALTER TABLE lecture DROP lecture_file_id");
        $this->addSql("ALTER TABLE lecture DROP lecture_type_id");
        $this->addSql("DROP INDEX IF EXISTS idx_c1677948f308dfc7");
        $this->addSql("DROP INDEX IF EXISTS idx_c1677948a58f2a38");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE lecture_genre_lecture DROP CONSTRAINT FK_26DE43029A6136");
        $this->addSql("DROP SEQUENCE lecture_genre_id_seq");
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE lecture_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE lecture_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("DROP TABLE lecture_genre_lecture");
        $this->addSql("DROP TABLE lecture_genre");
        $this->addSql("ALTER TABLE lecture ADD lecture_file_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD lecture_type_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD CONSTRAINT fk_c1677948a58f2a38 FOREIGN KEY (lecture_file_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE lecture ADD CONSTRAINT fk_c1677948f308dfc7 FOREIGN KEY (lecture_type_id) REFERENCES lecture_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX idx_c1677948f308dfc7 ON lecture (lecture_type_id)");
        $this->addSql("CREATE INDEX idx_c1677948a58f2a38 ON lecture (lecture_file_id)");
    }
}
