<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130306115903 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // skip if data is already in DB
        $stmt = $this->connection->query('SELECT COUNT(*) count FROM exhibit_category');
        $row = $stmt->fetch();
        $this->skipIf($row['count'] > 0, 'exhibit_category is not empty');

        // add data
        $this->addSql("
            INSERT INTO exhibit_category (id, title, root, lvl, lft, rgt)
            VALUES((select nextval('exhibit_category_id_seq')), :title, :root, :lvl, :lft, :rgt)
        ", array(
            'title' => 'Root',
            'root' => 1,
            'lvl' => 0,
            'lft' => 0,
            'rgt' => 1

        ));

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
