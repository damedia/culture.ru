<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130524175604 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE TABLE lecture_limit_slider_genre (lecture_id INT NOT NULL, lecturegenre_id INT NOT NULL, PRIMARY KEY(lecture_id, lecturegenre_id))");
        $this->addSql("CREATE INDEX IDX_73ADCC6335E32FCD ON lecture_limit_slider_genre (lecture_id)");
        $this->addSql("CREATE INDEX IDX_73ADCC639A6136 ON lecture_limit_slider_genre (lecturegenre_id)");
        $this->addSql("CREATE TABLE lecture_limit_featured_genre (lecture_id INT NOT NULL, lecturegenre_id INT NOT NULL, PRIMARY KEY(lecture_id, lecturegenre_id))");
        $this->addSql("CREATE INDEX IDX_1B55A16F35E32FCD ON lecture_limit_featured_genre (lecture_id)");
        $this->addSql("CREATE INDEX IDX_1B55A16F9A6136 ON lecture_limit_featured_genre (lecturegenre_id)");
        $this->addSql("ALTER TABLE lecture_limit_slider_genre ADD CONSTRAINT FK_73ADCC6335E32FCD FOREIGN KEY (lecture_id) REFERENCES lecture (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE lecture_limit_slider_genre ADD CONSTRAINT FK_73ADCC639A6136 FOREIGN KEY (lecturegenre_id) REFERENCES lecture_genre (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE lecture_limit_featured_genre ADD CONSTRAINT FK_1B55A16F35E32FCD FOREIGN KEY (lecture_id) REFERENCES lecture (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE lecture_limit_featured_genre ADD CONSTRAINT FK_1B55A16F9A6136 FOREIGN KEY (lecturegenre_id) REFERENCES lecture_genre (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE lecture ADD show_at_slider BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD show_at_featured BOOLEAN DEFAULT NULL");
        $this->addSql("UPDATE lecture SET show_at_slider = recommended, show_at_featured = recommended1, recommended = false");

        $this->addSql("ALTER TABLE lecture DROP COLUMN recommended1");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP TABLE lecture_limit_slider_genre");
        $this->addSql("DROP TABLE lecture_limit_featured_genre");
        $this->addSql("ALTER TABLE lecture ADD recommended1 BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture DROP show_at_slider");
        $this->addSql("ALTER TABLE lecture DROP show_at_featured");
    }
}
