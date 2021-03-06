<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121012155659 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $sql = "INSERT INTO lecture_super_type (
            id,
            name,
            code
        ) VALUES (
            nextval('lecture_super_type_id_seq'::regclass),
            :name,
            :code
        )";
        $this->addSql(
            $sql,
            array(
                'name' => 'Кино',
                'code' => 'LECTURE_SUPER_TYPE_CINEMA'
            )
        );

        $sql = "INSERT INTO lecture_category (
            id,
            title,
            root,
            lvl,
            lft,
            rgt
        ) VALUES(
            nextval('lecture_category_id_seq'::regclass),
            :title,
            :root,
            :lvl,
            :lft,
            :rgt
        )";
        $this->addSql($sql, array(
                'title' => '== Корневая категория (Кино) ==',
                'root' => 3,
                'lvl' => 0,
                'lft' => 0,
                'rgt' => 0,
            ));
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
