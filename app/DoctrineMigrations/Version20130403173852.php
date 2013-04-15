<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130403173852 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE atlas_tourist_cluster_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE atlas_object_tourist_cluster (object_id INT NOT NULL, touristcluster_id INT NOT NULL, PRIMARY KEY(object_id, touristcluster_id))");
        $this->addSql("CREATE INDEX IDX_D2B03FB8232D562B ON atlas_object_tourist_cluster (object_id)");
        $this->addSql("CREATE INDEX IDX_D2B03FB8E38B18EE ON atlas_object_tourist_cluster (touristcluster_id)");
        $this->addSql("CREATE TABLE atlas_tourist_cluster (id INT NOT NULL, title VARCHAR(255) NOT NULL, sort_index INT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("ALTER TABLE atlas_object_tourist_cluster ADD CONSTRAINT FK_D2B03FB8232D562B FOREIGN KEY (object_id) REFERENCES atlas_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE atlas_object_tourist_cluster ADD CONSTRAINT FK_D2B03FB8E38B18EE FOREIGN KEY (touristcluster_id) REFERENCES atlas_tourist_cluster (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE atlas_object_tourist_cluster DROP CONSTRAINT FK_D2B03FB8E38B18EE");
        $this->addSql("DROP SEQUENCE atlas_tourist_cluster_id_seq");
        $this->addSql("DROP TABLE atlas_object_tourist_cluster");
        $this->addSql("DROP TABLE atlas_tourist_cluster");
    }
}
