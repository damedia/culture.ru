<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130405145140 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE armd_theater_billboard_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE armd_theater_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE armd_theater_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE address_city_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE armd_theater_billboard (id INT NOT NULL, theater_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_41348404D70E4479 ON armd_theater_billboard (theater_id)");
        $this->addSql("CREATE TABLE armd_theater_category (id INT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE TABLE armd_theater (id INT NOT NULL, image_id INT DEFAULT NULL, interview_id INT DEFAULT NULL, city_id INT DEFAULT NULL, published BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, director VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, metro VARCHAR(255) DEFAULT NULL, ticketOfficeMode VARCHAR(255) DEFAULT NULL, latitude NUMERIC(15, 10) DEFAULT NULL, longitude NUMERIC(15, 10) DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_1C4DED123DA5256D ON armd_theater (image_id)");
        $this->addSql("CREATE INDEX IDX_1C4DED1255D69D95 ON armd_theater (interview_id)");
        $this->addSql("CREATE INDEX IDX_1C4DED128BAC62AF ON armd_theater (city_id)");
        $this->addSql("CREATE TABLE armd_theater_image (theater_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(theater_id, media_id))");
        $this->addSql("CREATE INDEX IDX_AD1D92C9D70E4479 ON armd_theater_image (theater_id)");
        $this->addSql("CREATE INDEX IDX_AD1D92C9EA9FDD75 ON armd_theater_image (media_id)");
        $this->addSql("CREATE TABLE armd_theater_theater_category (theater_id INT NOT NULL, theatercategory_id INT NOT NULL, PRIMARY KEY(theater_id, theatercategory_id))");
        $this->addSql("CREATE INDEX IDX_F75EB87FD70E4479 ON armd_theater_theater_category (theater_id)");
        $this->addSql("CREATE INDEX IDX_F75EB87FD95C3C7F ON armd_theater_theater_category (theatercategory_id)");
        $this->addSql("CREATE TABLE address_city (id INT NOT NULL, title VARCHAR(255) NOT NULL, sort_index INT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("ALTER TABLE armd_theater_billboard ADD CONSTRAINT FK_41348404D70E4479 FOREIGN KEY (theater_id) REFERENCES armd_theater (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_theater ADD CONSTRAINT FK_1C4DED123DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_theater ADD CONSTRAINT FK_1C4DED1255D69D95 FOREIGN KEY (interview_id) REFERENCES tvigle_video (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_theater ADD CONSTRAINT FK_1C4DED128BAC62AF FOREIGN KEY (city_id) REFERENCES address_city (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_theater_image ADD CONSTRAINT FK_AD1D92C9D70E4479 FOREIGN KEY (theater_id) REFERENCES armd_theater (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_theater_image ADD CONSTRAINT FK_AD1D92C9EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_theater_theater_category ADD CONSTRAINT FK_F75EB87FD70E4479 FOREIGN KEY (theater_id) REFERENCES armd_theater (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_theater_theater_category ADD CONSTRAINT FK_F75EB87FD95C3C7F FOREIGN KEY (theatercategory_id) REFERENCES armd_theater_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE armd_theater_theater_category DROP CONSTRAINT FK_F75EB87FD95C3C7F");
        $this->addSql("ALTER TABLE armd_theater_billboard DROP CONSTRAINT FK_41348404D70E4479");
        $this->addSql("ALTER TABLE armd_theater_image DROP CONSTRAINT FK_AD1D92C9D70E4479");
        $this->addSql("ALTER TABLE armd_theater_theater_category DROP CONSTRAINT FK_F75EB87FD70E4479");
        $this->addSql("ALTER TABLE armd_theater DROP CONSTRAINT FK_1C4DED128BAC62AF");
        $this->addSql("DROP SEQUENCE armd_theater_billboard_id_seq");
        $this->addSql("DROP SEQUENCE armd_theater_category_id_seq");
        $this->addSql("DROP SEQUENCE armd_theater_id_seq");
        $this->addSql("DROP SEQUENCE address_city_id_seq");
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("DROP TABLE armd_theater_billboard");
        $this->addSql("DROP TABLE armd_theater_category");
        $this->addSql("DROP TABLE armd_theater");
        $this->addSql("DROP TABLE armd_theater_image");
        $this->addSql("DROP TABLE armd_theater_theater_category");
        $this->addSql("DROP TABLE address_city");
    }
}
