<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130614123139 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM subscription_issue");
        $this->addSql("DELETE FROM user_mailing_list WHERE mailing_list_id IN (SELECT id FROM subscription_mailing_list WHERE type = 'new_content' OR type = 'custom')");
        $this->addSql("DELETE FROM subscription_mailing_list WHERE type = 'new_content' OR type = 'custom'");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
