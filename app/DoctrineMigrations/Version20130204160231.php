<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130204160231 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE atlas_category SET slug = 'type'  WHERE title = 'Object Type'");
        $this->addSql("UPDATE atlas_category SET slug = 'thematic'  WHERE title = 'Category'");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
