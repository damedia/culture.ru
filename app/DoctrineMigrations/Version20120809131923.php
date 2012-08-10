<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;


/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120809131923 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // abort if table doesn't exist
        $table = $schema->getTable('atlas_weekday');
        $this->abortIf(empty($table), 'Table atlas_weekday not found');

        // skip if data is already in DB
        $stmt = $this->connection->query('SELECT COUNT(*) count FROM atlas_weekday');
        $row = $stmt->fetch();
        $this->skipIf($row['count'] > 0, 'atlas_weekday is already filled');

        // add data
        $days = array('пн', 'вт', 'ср', 'чт' , 'пт', 'сб', 'вс');
        $id = 1;
        foreach($days as $day) {
            $this->addSql(
                'INSERT INTO atlas_weekday (id, name, sort_index) VALUES(:id, :name, :sort_index)',
                array(
                    'name' => $day,
                    'id' => $id,
                    'sort_index' => $id
                )
            );
            $id++;
        }
    }

    public function down(Schema $schema)
    {
        // remove data
        $table = $schema->getTable('atlas_weekday');
        $this->skipIf(empty($table), 'Table atlas_weekday not found');
        $this->addSql('DELETE FROM atlas_weekday');

    }


}
