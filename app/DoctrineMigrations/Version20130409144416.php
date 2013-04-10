<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130409144416 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE TABLE armd_theater_interviews (theater_id INT NOT NULL, tviglevideo_id INT NOT NULL, PRIMARY KEY(theater_id, tviglevideo_id))");
        $this->addSql("CREATE INDEX IDX_6971ED05D70E4479 ON armd_theater_interviews (theater_id)");
        $this->addSql("CREATE INDEX IDX_6971ED0565B1CCD0 ON armd_theater_interviews (tviglevideo_id)");
        $this->addSql("ALTER TABLE armd_theater_interviews ADD CONSTRAINT FK_6971ED05D70E4479 FOREIGN KEY (theater_id) REFERENCES armd_theater (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_theater_interviews ADD CONSTRAINT FK_6971ED0565B1CCD0 FOREIGN KEY (tviglevideo_id) REFERENCES tvigle_video (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("DROP TABLE armd_theater_image");
        $this->addSql("ALTER TABLE armd_theater RENAME COLUMN interview_id TO gallery_id");
        $this->addSql("ALTER TABLE armd_theater DROP CONSTRAINT fk_1c4ded1255d69d95");
        $this->addSql("DROP INDEX idx_1c4ded1255d69d95");
        $this->addSql("ALTER TABLE armd_theater ADD CONSTRAINT FK_1C4DED124E7AF8F FOREIGN KEY (gallery_id) REFERENCES media__gallery (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_1C4DED124E7AF8F ON armd_theater (gallery_id)");
        $this->addSql("ALTER TABLE content_perfomance ADD theater_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD CONSTRAINT FK_B6C6A549D70E4479 FOREIGN KEY (theater_id) REFERENCES armd_theater (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_B6C6A549D70E4479 ON content_perfomance (theater_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE armd_theater_image (theater_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(theater_id, media_id))");
        $this->addSql("CREATE INDEX idx_ad1d92c9ea9fdd75 ON armd_theater_image (media_id)");
        $this->addSql("CREATE INDEX idx_ad1d92c9d70e4479 ON armd_theater_image (theater_id)");
        $this->addSql("ALTER TABLE armd_theater_image ADD CONSTRAINT fk_ad1d92c9d70e4479 FOREIGN KEY (theater_id) REFERENCES armd_theater (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_theater_image ADD CONSTRAINT fk_ad1d92c9ea9fdd75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("DROP TABLE armd_theater_interviews");
        $this->addSql("ALTER TABLE content_perfomance DROP theater_id");
        $this->addSql("ALTER TABLE content_perfomance DROP CONSTRAINT FK_B6C6A549D70E4479");
        $this->addSql("DROP INDEX IDX_B6C6A549D70E4479");       
        $this->addSql("ALTER TABLE armd_theater RENAME COLUMN gallery_id TO interview_id");
        $this->addSql("ALTER TABLE armd_theater DROP CONSTRAINT FK_1C4DED124E7AF8F");
        $this->addSql("DROP INDEX IDX_1C4DED124E7AF8F");
        $this->addSql("ALTER TABLE armd_theater ADD CONSTRAINT fk_1c4ded1255d69d95 FOREIGN KEY (interview_id) REFERENCES tvigle_video (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX idx_1c4ded1255d69d95 ON armd_theater (interview_id)");
    }
}
