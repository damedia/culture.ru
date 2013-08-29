<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130826130749 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE TABLE sprojects_news (page_id INT NOT NULL, news_id INT NOT NULL, PRIMARY KEY(page_id, news_id))");
        $this->addSql("CREATE INDEX IDX_6B0C8C43C4663E4 ON sprojects_news (page_id)");
        $this->addSql("CREATE INDEX IDX_6B0C8C43B5A459A0 ON sprojects_news (news_id)");
        $this->addSql("ALTER TABLE sprojects_news ADD CONSTRAINT FK_6B0C8C43C4663E4 FOREIGN KEY (page_id) REFERENCES damedia_project_page (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE sprojects_news ADD CONSTRAINT FK_6B0C8C43B5A459A0 FOREIGN KEY (news_id) REFERENCES content_news (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql("DROP TABLE sprojects_news");
    }
}
