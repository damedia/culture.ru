<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130401141302 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE address_country_district_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE address_country_district (id INT NOT NULL, name VARCHAR(255) NOT NULL, code INT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("INSERT INTO address_country_district (id, name, code) VALUES
            (nextval('address_country_district_id_seq'), 'Дальневосточный', 320),
            (nextval('address_country_district_id_seq'), 'Центральный', 314),
            (nextval('address_country_district_id_seq'), 'Северо-Западный', 315),
            (nextval('address_country_district_id_seq'), 'Южный', 316),
            (nextval('address_country_district_id_seq'), 'Приволжский', 317),
            (nextval('address_country_district_id_seq'), 'Уральский', 318),
            (nextval('address_country_district_id_seq'), 'Сибирский', 319),
            (nextval('address_country_district_id_seq'), 'Северо-Кавказский', 4112)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE address_country_district_id_seq");
        $this->addSql("DROP TABLE address_country_district");
    }
}
