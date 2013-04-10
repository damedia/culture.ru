<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130409151124 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE armd_lesson_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE armd_lesson (id INT NOT NULL, image_id INT DEFAULT NULL, city_id INT DEFAULT NULL, museum_id INT DEFAULT NULL, education_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, published BOOLEAN DEFAULT NULL, title VARCHAR(255) NOT NULL, dates VARCHAR(255) NOT NULL, time VARCHAR(255) DEFAULT NULL, max_members INT DEFAULT NULL, place TEXT DEFAULT NULL, age VARCHAR(255) DEFAULT NULL, format VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, announce TEXT DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_6DC7290D3DA5256D ON armd_lesson (image_id)");
        $this->addSql("CREATE INDEX IDX_6DC7290D8BAC62AF ON armd_lesson (city_id)");
        $this->addSql("CREATE INDEX IDX_6DC7290D37E9D49E ON armd_lesson (museum_id)");
        $this->addSql("CREATE INDEX IDX_6DC7290D2CA1BD71 ON armd_lesson (education_id)");
        $this->addSql("CREATE INDEX IDX_6DC7290D23EDC87 ON armd_lesson (subject_id)");
        $this->addSql("CREATE TABLE armd_lesson_lesson_skill (lesson_id INT NOT NULL, lessonskill_id INT NOT NULL, PRIMARY KEY(lesson_id, lessonskill_id))");
        $this->addSql("CREATE INDEX IDX_20BF657DCDF80196 ON armd_lesson_lesson_skill (lesson_id)");
        $this->addSql("CREATE INDEX IDX_20BF657DD4B17298 ON armd_lesson_lesson_skill (lessonskill_id)");
        $this->addSql("ALTER TABLE armd_lesson ADD CONSTRAINT FK_6DC7290D3DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_lesson ADD CONSTRAINT FK_6DC7290D8BAC62AF FOREIGN KEY (city_id) REFERENCES address_city (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_lesson ADD CONSTRAINT FK_6DC7290D37E9D49E FOREIGN KEY (museum_id) REFERENCES armd_real_museum (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_lesson ADD CONSTRAINT FK_6DC7290D2CA1BD71 FOREIGN KEY (education_id) REFERENCES armd_education (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_lesson ADD CONSTRAINT FK_6DC7290D23EDC87 FOREIGN KEY (subject_id) REFERENCES armd_lesson_subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_lesson_lesson_skill ADD CONSTRAINT FK_20BF657DCDF80196 FOREIGN KEY (lesson_id) REFERENCES armd_lesson (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_lesson_lesson_skill ADD CONSTRAINT FK_20BF657DD4B17298 FOREIGN KEY (lessonskill_id) REFERENCES armd_lesson_skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE armd_lesson_lesson_skill DROP CONSTRAINT FK_20BF657DCDF80196");
        $this->addSql("DROP SEQUENCE armd_lesson_id_seq");
        $this->addSql("DROP TABLE armd_lesson");
        $this->addSql("DROP TABLE armd_lesson_lesson_skill");
    }
}
