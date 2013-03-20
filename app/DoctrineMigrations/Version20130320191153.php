<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130320191153 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
        $this->addSql("ALTER TABLE atlas_object ADD show_on_main BOOLEAN DEFAULT 'false' NOT NULL");
        $this->addSql("ALTER TABLE atlas_object ADD show_on_main_ord INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE atlas_object DROP show_at_homepage");
        $this->addSql("ALTER TABLE content_news ADD show_on_main BOOLEAN DEFAULT 'false' NOT NULL");
        $this->addSql("ALTER TABLE content_news ADD show_on_main_ord INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE content_news ALTER is_on_map SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE lecture ALTER show_on_main SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE lecture ALTER show_on_main_ord SET  DEFAULT 0");
        $this->addSql("ALTER TABLE armd_museum ADD show_on_main BOOLEAN DEFAULT 'false' NOT NULL");
        $this->addSql("ALTER TABLE armd_museum ADD show_on_main_ord INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE online_translation ALTER type SET  DEFAULT '0'");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("ALTER TABLE atlas_object ADD show_at_homepage BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE atlas_object DROP show_on_main");
        $this->addSql("ALTER TABLE atlas_object DROP show_on_main_ord");
        $this->addSql("ALTER TABLE armd_museum DROP show_on_main");
        $this->addSql("ALTER TABLE armd_museum DROP show_on_main_ord");
        $this->addSql("ALTER TABLE content_news DROP show_on_main");
        $this->addSql("ALTER TABLE content_news DROP show_on_main_ord");
        $this->addSql("ALTER TABLE content_news ALTER is_on_map SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE lecture ALTER show_on_main SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE lecture ALTER show_on_main_ord SET  DEFAULT 0");
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
        $this->addSql("ALTER TABLE online_translation ALTER type SET  DEFAULT '(0)::smallint'");
    }
}
