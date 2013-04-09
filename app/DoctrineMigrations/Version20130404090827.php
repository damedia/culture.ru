<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130404090827 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE content_perfomance ADD interview_video_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD interview_title VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD CONSTRAINT FK_B6C6A5494009C3AD FOREIGN KEY (interview_video_id) REFERENCES tvigle_video (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_B6C6A5494009C3AD ON content_perfomance (interview_video_id)");
        $this->addSql("ALTER TABLE content_perfomance ADD gallery_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD CONSTRAINT FK_B6C6A5494E7AF8F FOREIGN KEY (gallery_id) REFERENCES media__gallery (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_B6C6A5494E7AF8F ON content_perfomance (gallery_id)");   
        $this->addSql("ALTER TABLE content_perfomance ADD image_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD external_url VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD CONSTRAINT FK_B6C6A5493DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_B6C6A5493DA5256D ON content_perfomance (image_id)");  
        $this->addSql("ALTER TABLE content_perfomance ADD interview_description TEXT DEFAULT NULL");           
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("ALTER TABLE content_perfomance DROP interview_video_id");
        $this->addSql("ALTER TABLE content_perfomance DROP interview_title");
        $this->addSql("ALTER TABLE content_perfomance DROP CONSTRAINT FK_B6C6A5494009C3AD");
        $this->addSql("DROP INDEX IDX_B6C6A5494009C3AD");
        $this->addSql("ALTER TABLE content_perfomance DROP gallery_id");
        $this->addSql("ALTER TABLE content_perfomance DROP CONSTRAINT FK_B6C6A5494E7AF8F");
        $this->addSql("DROP INDEX IDX_B6C6A5494E7AF8F");     
        $this->addSql("ALTER TABLE content_perfomance DROP image_id");
        $this->addSql("ALTER TABLE content_perfomance DROP external_url");
        $this->addSql("ALTER TABLE content_perfomance DROP CONSTRAINT FK_B6C6A5493DA5256D");
        $this->addSql("DROP INDEX IDX_B6C6A5493DA5256D");      
        $this->addSql("ALTER TABLE content_perfomance DROP interview_description");     
    }
}
