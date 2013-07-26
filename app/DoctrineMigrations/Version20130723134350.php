<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130723134350 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE content_change_history_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE content_change_history (id INT NOT NULL, user_id INT DEFAULT NULL, entity_class VARCHAR(255) NOT NULL, entity_id INT NOT NULL, changes TEXT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_ip VARCHAR(255) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_BD562836A76ED395 ON content_change_history (user_id)");
        $this->addSql("COMMENT ON COLUMN content_change_history.changes IS '(DC2Type:array)'");
        $this->addSql("ALTER TABLE content_change_history ADD CONSTRAINT FK_BD562836A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
        $this->addSql("ALTER TABLE atlas_object ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE content_news ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE content_chronicle_event ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_lesson ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_museum ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_museum_guide ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_real_museum ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_war_gallery_member ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE online_translation ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE art_object ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_person ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_theater ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD corrected BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE tourist_route ADD corrected BOOLEAN DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE content_change_history_id_seq CASCADE");
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("DROP TABLE content_change_history");
        $this->addSql("ALTER TABLE armd_lesson DROP corrected");
        $this->addSql("ALTER TABLE armd_museum DROP corrected");
        $this->addSql("ALTER TABLE armd_museum_guide DROP corrected");
        $this->addSql("ALTER TABLE armd_person DROP corrected");
        $this->addSql("ALTER TABLE art_object DROP corrected");
        $this->addSql("ALTER TABLE armd_real_museum DROP corrected");
        $this->addSql("ALTER TABLE armd_war_gallery_member DROP corrected");
        $this->addSql("ALTER TABLE atlas_object DROP corrected");
        $this->addSql("ALTER TABLE content_chronicle_event DROP corrected");
        $this->addSql("ALTER TABLE content_perfomance DROP corrected");
        $this->addSql("ALTER TABLE content_news DROP corrected");
        $this->addSql("ALTER TABLE lecture DROP corrected");
        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
        $this->addSql("ALTER TABLE online_translation DROP corrected");
        $this->addSql("ALTER TABLE tourist_route DROP corrected");
        $this->addSql("ALTER TABLE armd_theater DROP corrected");
    }
}
