<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130528132601 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "INSERT INTO lecture_super_type (id, name, code, template)
            VALUES (nextval('lecture_category_id_seq'), :name, :code, :template)",
            array('name' => 'Видео', 'code' => 'LECTURE_SUPER_TYPE_NEWS', 'template' => 'news')
        );


    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
