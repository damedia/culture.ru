<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130125122700 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            UPDATE tagging
            SET resource_type = 'armd_atlas_object'
            WHERE resource_id::int IN (
                SELECT o.id
                FROM tagging t
                INNER JOIN atlas_object o ON o.id = t.resource_id::int
                INNER JOIN tag ON tag.id = t.tag_id
                WHERE resource_type = 'armd_tag_global'
                AND o.show_at_russian_image =  TRUE
            )
        ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
