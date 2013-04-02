<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130328173931 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE atlas_object ALTER show_on_main DROP DEFAULT ");
        $this->addSql("ALTER TABLE atlas_object ALTER show_on_main_ord  DROP DEFAULT ");
        $this->addSql("ALTER TABLE content_news ALTER is_on_map  DROP DEFAULT ");
        $this->addSql("ALTER TABLE content_news ALTER show_on_main  DROP DEFAULT ");
        $this->addSql("ALTER TABLE content_news ALTER show_on_main_ord  DROP DEFAULT ");
        $this->addSql("ALTER TABLE lecture ALTER is_top_100_film SET NOT NULL");
        $this->addSql("ALTER TABLE lecture ALTER show_on_main  DROP DEFAULT ");
        $this->addSql("ALTER TABLE lecture ALTER show_on_main_ord  DROP DEFAULT ");
        $this->addSql("ALTER TABLE lecture_category ADD system_slug VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_museum ALTER show_on_main  DROP DEFAULT ");
        $this->addSql("ALTER TABLE armd_museum ALTER show_on_main_ord  DROP DEFAULT ");
        $this->addSql("ALTER TABLE tag ALTER istechnical  DROP DEFAULT ");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("ALTER TABLE armd_museum ALTER show_on_main SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE armd_museum ALTER show_on_main_ord SET  DEFAULT 0");
        $this->addSql("ALTER TABLE content_news ALTER is_on_map SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE content_news ALTER show_on_main SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE content_news ALTER show_on_main_ord SET  DEFAULT 0");
        $this->addSql("ALTER TABLE lecture ALTER is_top_100_film DROP NOT NULL");
        $this->addSql("ALTER TABLE lecture ALTER show_on_main SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE lecture ALTER show_on_main_ord SET  DEFAULT 0");
        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
        $this->addSql("ALTER TABLE Tag ALTER isTechnical SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
        $this->addSql("ALTER TABLE lecture_category DROP system_slug");
        $this->addSql("ALTER TABLE atlas_object ALTER show_on_main SET  DEFAULT 'false'");
        $this->addSql("ALTER TABLE atlas_object ALTER show_on_main_ord SET  DEFAULT 0");
    }
}
