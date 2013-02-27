<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130227130503 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE tag SET istechnical = TRUE WHERE name ~ '^o[\d]+$' OR name ~ '^l[\d]+$'");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
