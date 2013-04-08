<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130327083500 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE lecture SET lecture_type_id  = 1 WHERE lecture_type_id = 4');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
