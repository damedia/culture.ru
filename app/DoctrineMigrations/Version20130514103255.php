<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130514103255 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE TABLE atlas_object_media_video (object_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(object_id, media_id))");
        $this->addSql("CREATE INDEX IDX_1CF090CC232D562B ON atlas_object_media_video (object_id)");
        $this->addSql("CREATE INDEX IDX_1CF090CCEA9FDD75 ON atlas_object_media_video (media_id)");
        $this->addSql("ALTER TABLE atlas_object_media_video ADD CONSTRAINT FK_1CF090CC232D562B FOREIGN KEY (object_id) REFERENCES atlas_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE atlas_object_media_video ADD CONSTRAINT FK_1CF090CCEA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");

        $this->addSql("CREATE TABLE art_object_media_video (artobject_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(artobject_id, media_id))");
        $this->addSql("CREATE INDEX IDX_6B425FCF54EA6A5D ON art_object_media_video (artobject_id)");
        $this->addSql("CREATE INDEX IDX_6B425FCFEA9FDD75 ON art_object_media_video (media_id)");
        $this->addSql("ALTER TABLE art_object_media_video ADD CONSTRAINT FK_6B425FCF54EA6A5D FOREIGN KEY (artobject_id) REFERENCES art_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE art_object_media_video ADD CONSTRAINT FK_6B425FCFEA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");

        $this->addSql("ALTER TABLE content_perfomance ADD perfomance_media_video_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD trailer_media_video_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD interview_media_video_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD CONSTRAINT FK_B6C6A549EB95557F FOREIGN KEY (perfomance_media_video_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE content_perfomance ADD CONSTRAINT FK_B6C6A54925BE8291 FOREIGN KEY (trailer_media_video_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE content_perfomance ADD CONSTRAINT FK_B6C6A549EC3AE39D FOREIGN KEY (interview_media_video_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_B6C6A549EB95557F ON content_perfomance (perfomance_media_video_id)");
        $this->addSql("CREATE INDEX IDX_B6C6A54925BE8291 ON content_perfomance (trailer_media_video_id)");
        $this->addSql("CREATE INDEX IDX_B6C6A549EC3AE39D ON content_perfomance (interview_media_video_id)");

        $this->addSql("CREATE TABLE armd_theater_media_interviews (theater_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(theater_id, media_id))");
        $this->addSql("CREATE INDEX IDX_4CB81202D70E4479 ON armd_theater_media_interviews (theater_id)");
        $this->addSql("CREATE INDEX IDX_4CB81202EA9FDD75 ON armd_theater_media_interviews (media_id)");
        $this->addSql("ALTER TABLE armd_theater_media_interviews ADD CONSTRAINT FK_4CB81202D70E4479 FOREIGN KEY (theater_id) REFERENCES armd_theater (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_theater_media_interviews ADD CONSTRAINT FK_4CB81202EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");

        $this->addSql("CREATE TABLE tourist_route__media_video (route_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(route_id, media_id))");
        $this->addSql("CREATE INDEX IDX_3A91070834ECB4E6 ON tourist_route__media_video (route_id)");
        $this->addSql("CREATE INDEX IDX_3A910708EA9FDD75 ON tourist_route__media_video (media_id)");
        $this->addSql("ALTER TABLE tourist_route__media_video ADD CONSTRAINT FK_3A91070834ECB4E6 FOREIGN KEY (route_id) REFERENCES tourist_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__media_video ADD CONSTRAINT FK_3A910708EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP TABLE atlas_object_media_video");
        
        $this->addSql("DROP TABLE art_object_media_video");
        
        $this->addSql("ALTER TABLE content_perfomance DROP perfomance_media_video_id");
        $this->addSql("ALTER TABLE content_perfomance DROP trailer_media_video_id");
        $this->addSql("ALTER TABLE content_perfomance DROP interview_media_video_id");

        $this->addSql("DROP TABLE armd_theater_media_interviews");

        $this->addSql("DROP TABLE tourist_route__media_video");
    }
}
