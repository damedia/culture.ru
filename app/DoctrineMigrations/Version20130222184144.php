<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130222184144 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE online_translation_notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE online_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE online_translation_notification (id INT NOT NULL, period INT NOT NULL, email VARCHAR(255) NOT NULL, onlineTranslation_id INT DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_F47E2ABDA2D15FEC ON online_translation_notification (onlineTranslation_id)");
        $this->addSql("CREATE UNIQUE INDEX notification_idx ON online_translation_notification (onlinetranslation_id, email, period)");
        $this->addSql("CREATE TABLE online_translation (id INT NOT NULL, image_id INT DEFAULT NULL, published BOOLEAN NOT NULL, type SMALLINT NOT NULL, title VARCHAR(255) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, location VARCHAR(255) NOT NULL, short_description VARCHAR(255) NOT NULL, full_description TEXT NOT NULL, data_code TEXT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_2BDA82343DA5256D ON online_translation (image_id)");
        $this->addSql("ALTER TABLE online_translation_notification ADD CONSTRAINT FK_F47E2ABDA2D15FEC FOREIGN KEY (onlineTranslation_id) REFERENCES online_translation (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE online_translation ADD CONSTRAINT FK_2BDA82343DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE online_translation_notification DROP CONSTRAINT FK_F47E2ABDA2D15FEC");
        $this->addSql("DROP SEQUENCE online_translation_notification_id_seq");
        $this->addSql("DROP SEQUENCE online_translation_id_seq");
        $this->addSql("DROP TABLE online_translation_notification");
        $this->addSql("DROP TABLE online_translation");
    }
}
