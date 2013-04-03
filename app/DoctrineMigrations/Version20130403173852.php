<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130403173852 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE atlas_tourist_cluster_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
//        $this->addSql("CREATE TABLE atlas_object_stuff (object_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(object_id, media_id))");
//        $this->addSql("CREATE INDEX IDX_3DB866D232D562B ON atlas_object_stuff (object_id)");
//        $this->addSql("CREATE INDEX IDX_3DB866DEA9FDD75 ON atlas_object_stuff (media_id)");
        $this->addSql("CREATE TABLE atlas_object_tourist_cluster (object_id INT NOT NULL, touristcluster_id INT NOT NULL, PRIMARY KEY(object_id, touristcluster_id))");
        $this->addSql("CREATE INDEX IDX_D2B03FB8232D562B ON atlas_object_tourist_cluster (object_id)");
        $this->addSql("CREATE INDEX IDX_D2B03FB8E38B18EE ON atlas_object_tourist_cluster (touristcluster_id)");
        $this->addSql("CREATE TABLE atlas_tourist_cluster (id INT NOT NULL, title VARCHAR(255) NOT NULL, sort_index INT NOT NULL, PRIMARY KEY(id))");
//        $this->addSql("CREATE TABLE content_news_stuff (news_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(news_id, media_id))");
//        $this->addSql("CREATE INDEX IDX_BAC7C992B5A459A0 ON content_news_stuff (news_id)");
//        $this->addSql("CREATE INDEX IDX_BAC7C992EA9FDD75 ON content_news_stuff (media_id)");
//        $this->addSql("CREATE TABLE lecture_stuff (lecture_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(lecture_id, media_id))");
//        $this->addSql("CREATE INDEX IDX_F22C2DD035E32FCD ON lecture_stuff (lecture_id)");
//        $this->addSql("CREATE INDEX IDX_F22C2DD0EA9FDD75 ON lecture_stuff (media_id)");
//        $this->addSql("ALTER TABLE atlas_object_stuff ADD CONSTRAINT FK_3DB866D232D562B FOREIGN KEY (object_id) REFERENCES atlas_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
//        $this->addSql("ALTER TABLE atlas_object_stuff ADD CONSTRAINT FK_3DB866DEA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE atlas_object_tourist_cluster ADD CONSTRAINT FK_D2B03FB8232D562B FOREIGN KEY (object_id) REFERENCES atlas_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE atlas_object_tourist_cluster ADD CONSTRAINT FK_D2B03FB8E38B18EE FOREIGN KEY (touristcluster_id) REFERENCES atlas_tourist_cluster (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
//        $this->addSql("ALTER TABLE content_news_stuff ADD CONSTRAINT FK_BAC7C992B5A459A0 FOREIGN KEY (news_id) REFERENCES content_news (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
//        $this->addSql("ALTER TABLE content_news_stuff ADD CONSTRAINT FK_BAC7C992EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
//        $this->addSql("ALTER TABLE lecture_stuff ADD CONSTRAINT FK_F22C2DD035E32FCD FOREIGN KEY (lecture_id) REFERENCES lecture (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
//        $this->addSql("ALTER TABLE lecture_stuff ADD CONSTRAINT FK_F22C2DD0EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
//        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
//        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
//        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
//        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
//        $this->addSql("ALTER TABLE atlas_object ADD show_on_main BOOLEAN DEFAULT 'false' NOT NULL");
//        $this->addSql("ALTER TABLE atlas_object ADD show_on_main_ord INT DEFAULT 0 NOT NULL");
//        $this->addSql("ALTER TABLE atlas_object DROP show_at_homepage");
//        $this->addSql("ALTER TABLE content_news ADD show_on_main BOOLEAN DEFAULT 'false' NOT NULL");
//        $this->addSql("ALTER TABLE content_news ADD show_on_main_ord INT DEFAULT 0 NOT NULL");
//        $this->addSql("ALTER TABLE content_news ALTER is_on_map SET  DEFAULT 'false'");
//        $this->addSql("ALTER TABLE lecture ADD show_on_main BOOLEAN DEFAULT 'false' NOT NULL");
//        $this->addSql("ALTER TABLE lecture ADD show_on_main_ord INT DEFAULT 0 NOT NULL");
//        $this->addSql("ALTER TABLE armd_museum ADD show_on_main BOOLEAN DEFAULT 'false' NOT NULL");
//        $this->addSql("ALTER TABLE armd_museum ADD show_on_main_ord INT DEFAULT 0 NOT NULL");
//        $this->addSql("ALTER TABLE tag ALTER istechnical SET  DEFAULT 'false'");
//        $this->addSql("ALTER TABLE online_translation ALTER type SET  DEFAULT '0'");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE atlas_object_tourist_cluster DROP CONSTRAINT FK_D2B03FB8E38B18EE");
        $this->addSql("DROP SEQUENCE atlas_tourist_cluster_id_seq");
//        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
//        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
//        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
//        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
//        $this->addSql("DROP TABLE atlas_object_stuff");
        $this->addSql("DROP TABLE atlas_object_tourist_cluster");
        $this->addSql("DROP TABLE atlas_tourist_cluster");
//        $this->addSql("DROP TABLE content_news_stuff");
//        $this->addSql("DROP TABLE lecture_stuff");
//        $this->addSql("ALTER TABLE atlas_object ADD show_at_homepage BOOLEAN DEFAULT NULL");
//        $this->addSql("ALTER TABLE atlas_object DROP show_on_main");
//        $this->addSql("ALTER TABLE atlas_object DROP show_on_main_ord");
//        $this->addSql("ALTER TABLE armd_museum DROP show_on_main");
//        $this->addSql("ALTER TABLE armd_museum DROP show_on_main_ord");
//        $this->addSql("ALTER TABLE content_news DROP show_on_main");
//        $this->addSql("ALTER TABLE content_news DROP show_on_main_ord");
//        $this->addSql("ALTER TABLE content_news ALTER is_on_map SET  DEFAULT 'false'");
//        $this->addSql("ALTER TABLE lecture DROP show_on_main");
//        $this->addSql("ALTER TABLE lecture DROP show_on_main_ord");
//        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
//        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
//        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
//        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
//        $this->addSql("ALTER TABLE Tag ALTER isTechnical SET  DEFAULT 'false'");
//        $this->addSql("ALTER TABLE online_translation ALTER type SET  DEFAULT '(0)::smallint'");
    }
}
