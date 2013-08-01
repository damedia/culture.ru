<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130801133554 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER SEQUENCE damedia_project_template_id_seq RESTART WITH 10");
        $this->addSql("ALTER SEQUENCE damedia_project_page_id_seq RESTART WITH 30");
        $this->addSql("ALTER SEQUENCE damedia_project_block_id_seq RESTART WITH 100");
        $this->addSql("ALTER SEQUENCE damedia_project_chunk_id_seq RESTART WITH 100");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
