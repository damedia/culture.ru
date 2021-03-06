<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121026125426 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        // $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");
        $this->addSql("CREATE SEQUENCE content_press_archive_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE content_press_archive (id INT NOT NULL, image_id INT DEFAULT NULL, file_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, date_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_176E9B053DA5256D ON content_press_archive (image_id)");
        $this->addSql("CREATE INDEX IDX_176E9B0593CB796C ON content_press_archive (file_id)");
        $this->addSql("ALTER TABLE content_press_archive ADD CONSTRAINT FK_176E9B053DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE content_press_archive ADD CONSTRAINT FK_176E9B0593CB796C FOREIGN KEY (file_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        // $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");
        $this->addSql("DROP SEQUENCE content_press_archive_id_seq");
        $this->addSql("DROP TABLE content_press_archive");
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
    }
}
