<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130528183702 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            INSERT INTO subscription_mailing_list (id, periodically, title, description, type, enabled)
            VALUES(nextval('subscription_mailing_list_id_seq'), true, 'Новости Культуры', 'Новости Культуры', 'new_news', true)"
        );
        $this->addSql("
            INSERT INTO subscription_mailing_list (id, periodically, title, description, type, enabled)
            VALUES(nextval('subscription_mailing_list_id_seq'), true, 'Новый контент', 'Новый контент', 'new_content', true)"
        );
        $this->addSql("
            INSERT INTO subscription_mailing_list (id, periodically, title, description, type, enabled)
            VALUES(nextval('subscription_mailing_list_id_seq'), false, 'Новости проекта', 'Новости проекта', 'custom', true)"
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
