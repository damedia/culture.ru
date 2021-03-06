<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120827012644 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $stmt = $this->connection->query("
            SELECT id
            FROM lecture_super_type
            WHERE code = 'LECTURE_SUPER_TYPE_LECTURE'
        ");
        $row = $stmt->fetch();
        $lectureSuperTypeId = $row['id'];

        $stmt = $this->connection->query("
            SELECT id
            FROM lecture_super_type
            WHERE code = 'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION'
        ");
        $row = $stmt->fetch();
        $translationSuperTypeId = $row['id'];

        $this->addSql("
            UPDATE lecture
            SET lecture_super_type_id = :lecture_super_type_id
            WHERE lecture_super_type_id IS NULL
            ",
            array(
                'lecture_super_type_id' => $lectureSuperTypeId
            )
        );

        $this->addSql("
            UPDATE lecture_category
            SET super_type_id = :lecture_super_type_id
            WHERE super_type_id IS NULL
            AND parent_id IS NULL
            ",
            array(
                'lecture_super_type_id' => $lectureSuperTypeId
            )
        );

        $stmt = $this->connection->query("
            SELECT COUNT(*) count
            FROM lecture_category lc
            INNER JOIN lecture_super_type lst ON lst.id = lc.super_type_id
            WHERE parent_id IS NULL
            AND lst.code = 'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION'

        ");
        $row = $stmt->fetch();
        if($row['count'] == 0) {

            $this->addSql("
                INSERT INTO lecture_category (id, title, root, lvl, lft, rgt, super_type_id)
                VALUES (
                    nextval('lecture_category_id_seq'::regclass),
                    '=== Корневая категория (Трансляции) ===',
                    2,
                    0,
                    1,
                    2,
                    :translation_super_type_id
                )
            ",
                array(
                    'translation_super_type_id' => $translationSuperTypeId
                )
            );
        }

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
