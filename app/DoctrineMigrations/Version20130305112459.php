<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class Version20130305112459 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("CREATE TABLE atlas_object_stuff (object_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(object_id, media_id))");
        $this->addSql("CREATE INDEX IDX_3DB866D232D562B ON atlas_object_stuff (object_id)");
        $this->addSql("CREATE INDEX IDX_3DB866DEA9FDD75 ON atlas_object_stuff (media_id)");
        $this->addSql("CREATE TABLE content_news_stuff (news_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(news_id, media_id))");
        $this->addSql("CREATE INDEX IDX_BAC7C992B5A459A0 ON content_news_stuff (news_id)");
        $this->addSql("CREATE INDEX IDX_BAC7C992EA9FDD75 ON content_news_stuff (media_id)");
        $this->addSql("CREATE TABLE lecture_stuff (lecture_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(lecture_id, media_id))");
        $this->addSql("CREATE INDEX IDX_F22C2DD035E32FCD ON lecture_stuff (lecture_id)");
        $this->addSql("CREATE INDEX IDX_F22C2DD0EA9FDD75 ON lecture_stuff (media_id)");
        $this->addSql("ALTER TABLE atlas_object_stuff ADD CONSTRAINT FK_3DB866D232D562B FOREIGN KEY (object_id) REFERENCES atlas_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE atlas_object_stuff ADD CONSTRAINT FK_3DB866DEA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE content_news_stuff ADD CONSTRAINT FK_BAC7C992B5A459A0 FOREIGN KEY (news_id) REFERENCES content_news (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE content_news_stuff ADD CONSTRAINT FK_BAC7C992EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE lecture_stuff ADD CONSTRAINT FK_F22C2DD035E32FCD FOREIGN KEY (lecture_id) REFERENCES lecture (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE lecture_stuff ADD CONSTRAINT FK_F22C2DD0EA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("DROP TABLE atlas_object_stuff");
        $this->addSql("DROP TABLE content_news_stuff");
        $this->addSql("DROP TABLE lecture_stuff");
    }
}
