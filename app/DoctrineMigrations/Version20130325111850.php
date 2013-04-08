<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130325111850 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $stmt = $this->connection->query('SELECT COUNT(*) count FROM exhibit_category');
        $row = $stmt->fetch();
        
        if ($row['count'] == 0) {
            $this->addSql("
                INSERT INTO exhibit_category (id, published, title, root, lvl, lft, rgt)
                VALUES((select nextval('exhibit_category_id_seq')), true, :title, :root, :lvl, :lft, :rgt)
            ", array(
                'title' => '== Корневая категория ==',
                'root' => 1,
                'lvl' => 0,
                'lft' => 0,
                'rgt' => 1
            ));
        }
        
        $stmt = $this->connection->query("SELECT COUNT(*) count FROM armd_person_type WHERE slug = 'art_gallery_author'");
        $row = $stmt->fetch();
        
        if ($row['count'] == 0) {
            $this->addSql("
                INSERT INTO armd_person_type (id, title, slug)
                VALUES((select nextval('armd_person_type_id_seq')), :title, :slug)
            ", array(
                'title' => 'Автор',
                'slug' => 'art_gallery_author'
            ));
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
