<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130319193252 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            UPDATE content_news_category
            SET title='Статьи'
            WHERE title='Репортажи'
        ");


    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
