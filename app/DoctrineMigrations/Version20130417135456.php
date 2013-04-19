<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130417135456 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE TABLE armd_lesson_lesson_subject (lesson_id INT NOT NULL, lessonsubject_id INT NOT NULL, PRIMARY KEY(lesson_id, lessonsubject_id))");
        $this->addSql("CREATE INDEX IDX_9B9E1BC4CDF80196 ON armd_lesson_lesson_subject (lesson_id)");
        $this->addSql("CREATE INDEX IDX_9B9E1BC42B819210 ON armd_lesson_lesson_subject (lessonsubject_id)");
        $this->addSql("ALTER TABLE armd_lesson_lesson_subject ADD CONSTRAINT FK_9B9E1BC4CDF80196 FOREIGN KEY (lesson_id) REFERENCES armd_lesson (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_lesson_lesson_subject ADD CONSTRAINT FK_9B9E1BC42B819210 FOREIGN KEY (lessonsubject_id) REFERENCES armd_lesson_subject (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("DROP INDEX idx_6dc7290d23edc87");
        $this->addSql("ALTER TABLE armd_lesson DROP CONSTRAINT fk_6dc7290d23edc87");
        $this->addSql("ALTER TABLE armd_lesson DROP subject_id");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP TABLE armd_lesson_lesson_subject");
        $this->addSql("ALTER TABLE armd_lesson ADD subject_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_lesson ADD CONSTRAINT fk_6dc7290d23edc87 FOREIGN KEY (subject_id) REFERENCES armd_lesson_subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX idx_6dc7290d23edc87 ON armd_lesson (subject_id)");
    }
}
