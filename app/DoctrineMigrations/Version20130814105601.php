<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130814105601 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE blogs_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE press_center_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE actual_info_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE blogs (id INT NOT NULL, top_image_id INT DEFAULT NULL, user_id INT DEFAULT NULL, thread_id VARCHAR(255) DEFAULT NULL, title VARCHAR(255) NOT NULL, lead TEXT NOT NULL, content TEXT NOT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_F41BCA709C90EF21 ON blogs (top_image_id)");
        $this->addSql("CREATE INDEX IDX_F41BCA70A76ED395 ON blogs (user_id)");
        $this->addSql("CREATE INDEX IDX_F41BCA70E2904019 ON blogs (thread_id)");
        $this->addSql("CREATE TABLE press_center (id INT NOT NULL, image_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, show_on_main BOOLEAN NOT NULL, show_on_main_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, show_on_main_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_141D9273DA5256D ON press_center (image_id)");
        $this->addSql("CREATE TABLE actual_info (id INT NOT NULL, video_id INT DEFAULT NULL, image_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, text TEXT DEFAULT NULL, show_on_main BOOLEAN NOT NULL, show_on_main_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, show_on_main_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_8B8B746B29C1004E ON actual_info (video_id)");
        $this->addSql("CREATE INDEX IDX_8B8B746B3DA5256D ON actual_info (image_id)");
        $this->addSql("ALTER TABLE blogs ADD CONSTRAINT FK_F41BCA709C90EF21 FOREIGN KEY (top_image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE blogs ADD CONSTRAINT FK_F41BCA70A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE blogs ADD CONSTRAINT FK_F41BCA70E2904019 FOREIGN KEY (thread_id) REFERENCES comment_thread (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE press_center ADD CONSTRAINT FK_141D9273DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE actual_info ADD CONSTRAINT FK_8B8B746B29C1004E FOREIGN KEY (video_id) REFERENCES tvigle_video (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE actual_info ADD CONSTRAINT FK_8B8B746B3DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ADD avatar_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ADD CONSTRAINT FK_C560D76186383B10 FOREIGN KEY (avatar_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_C560D76186383B10 ON fos_user_user (avatar_id)");
        $this->addSql("ALTER TABLE atlas_object ADD show_on_main_as_recommended_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE atlas_object ADD show_on_main_as_recommended_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE atlas_object ADD show_on_main_as_novel BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE atlas_object ADD show_on_main_as_novel_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE atlas_object ADD show_on_main_as_novel_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE atlas_object ADD show_on_main_as_novel_ord INT DEFAULT NULL");
        $this->addSql("ALTER TABLE atlas_object RENAME COLUMN show_on_main TO show_on_main_as_recommended");
        $this->addSql("ALTER TABLE atlas_object RENAME COLUMN show_on_main_ord TO show_on_main_as_recommended_ord");
        $this->addSql("ALTER TABLE lecture ADD show_on_main_as_recommended BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD show_on_main_as_recommended_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD show_on_main_as_recommended_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD show_on_main_as_recommended_ord INT DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD show_on_main_as_for_children_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD show_on_main_as_for_children_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture RENAME COLUMN show_on_main TO show_on_main_as_for_children");
        $this->addSql("ALTER TABLE lecture RENAME COLUMN show_on_main_ord TO show_on_main_as_for_children_ord");
        $this->addSql("ALTER TABLE armd_museum ADD show_on_main_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE armd_museum ADD show_on_main_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD show_on_main BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD show_on_main_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE content_perfomance ADD show_on_main_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE damedia_project_page ADD show_on_main BOOLEAN DEFAULT NULL");
        $this->addSql("ALTER TABLE damedia_project_page ADD show_on_main_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
        $this->addSql("ALTER TABLE damedia_project_page ADD show_on_main_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE blogs_id_seq CASCADE");
        $this->addSql("DROP SEQUENCE press_center_id_seq CASCADE");
        $this->addSql("DROP SEQUENCE actual_info_id_seq CASCADE");
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("DROP TABLE blogs");
        $this->addSql("DROP TABLE press_center");
        $this->addSql("DROP TABLE actual_info");
        $this->addSql("ALTER TABLE armd_museum DROP show_on_main_from");
        $this->addSql("ALTER TABLE armd_museum DROP show_on_main_to");
        $this->addSql("ALTER TABLE damedia_project_page DROP show_on_main");
        $this->addSql("ALTER TABLE damedia_project_page DROP show_on_main_from");
        $this->addSql("ALTER TABLE damedia_project_page DROP show_on_main_to");
        $this->addSql("ALTER TABLE content_perfomance DROP show_on_main");
        $this->addSql("ALTER TABLE content_perfomance DROP show_on_main_from");
        $this->addSql("ALTER TABLE content_perfomance DROP show_on_main_to");
        $this->addSql("ALTER TABLE fos_user_user DROP avatar_id");
        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user DROP CONSTRAINT FK_C560D76186383B10");
        $this->addSql("DROP INDEX IDX_C560D76186383B10");
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
        $this->addSql("ALTER TABLE atlas_object DROP show_on_main_as_recommended_from");
        $this->addSql("ALTER TABLE atlas_object DROP show_on_main_as_recommended_to");
        $this->addSql("ALTER TABLE atlas_object DROP show_on_main_as_novel");
        $this->addSql("ALTER TABLE atlas_object DROP show_on_main_as_novel_from");
        $this->addSql("ALTER TABLE atlas_object DROP show_on_main_as_novel_to");
        $this->addSql("ALTER TABLE atlas_object DROP show_on_main_as_novel_ord");
        $this->addSql("ALTER TABLE atlas_object RENAME COLUMN show_on_main_as_recommended TO show_on_main");
        $this->addSql("ALTER TABLE atlas_object RENAME COLUMN show_on_main_as_recommended_ord TO show_on_main_ord");
        $this->addSql("ALTER TABLE lecture DROP show_on_main_as_recommended");
        $this->addSql("ALTER TABLE lecture DROP show_on_main_as_recommended_from");
        $this->addSql("ALTER TABLE lecture DROP show_on_main_as_recommended_to");
        $this->addSql("ALTER TABLE lecture DROP show_on_main_as_recommended_ord");
        $this->addSql("ALTER TABLE lecture DROP show_on_main_as_for_children_from");
        $this->addSql("ALTER TABLE lecture DROP show_on_main_as_for_children_to");
        $this->addSql("ALTER TABLE lecture RENAME COLUMN show_on_main_as_for_children TO show_on_main");
        $this->addSql("ALTER TABLE lecture RENAME COLUMN show_on_main_as_for_children_ord TO show_on_main_ord");
    }
}
