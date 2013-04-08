<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130325111441 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE armd_real_museum_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE exhibit_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE art_object_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE armd_person_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE armd_person_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE armd_real_museum (id INT NOT NULL, image_id INT DEFAULT NULL, region_id INT DEFAULT NULL, category_id INT DEFAULT NULL, atlas_object_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_350ECE263DA5256D ON armd_real_museum (image_id)");
        $this->addSql("CREATE INDEX IDX_350ECE2698260155 ON armd_real_museum (region_id)");
        $this->addSql("CREATE INDEX IDX_350ECE2612469DE2 ON armd_real_museum (category_id)");
        $this->addSql("CREATE INDEX IDX_350ECE26129049C2 ON armd_real_museum (atlas_object_id)");
        $this->addSql("CREATE TABLE armd_real_museum_virtual_tour (realmuseum_id INT NOT NULL, museum_id INT NOT NULL, PRIMARY KEY(realmuseum_id, museum_id))");
        $this->addSql("CREATE INDEX IDX_AFA07BA492F7C848 ON armd_real_museum_virtual_tour (realmuseum_id)");
        $this->addSql("CREATE INDEX IDX_AFA07BA44B52E5B5 ON armd_real_museum_virtual_tour (museum_id)");
        $this->addSql("CREATE TABLE exhibit_category (id INT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, published BOOLEAN NOT NULL, root INT DEFAULT NULL, lvl INT NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_8E437B81727ACA70 ON exhibit_category (parent_id)");
        $this->addSql("CREATE TABLE art_object (id INT NOT NULL, primary_image_id INT DEFAULT NULL, museum_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, date DATE NOT NULL, description TEXT NOT NULL, published BOOLEAN NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_E712E0101CDA489C ON art_object (primary_image_id)");
        $this->addSql("CREATE INDEX IDX_E712E0104B52E5B5 ON art_object (museum_id)");
        $this->addSql("CREATE TABLE art_object_video (artobject_id INT NOT NULL, tviglevideo_id INT NOT NULL, PRIMARY KEY(artobject_id, tviglevideo_id))");
        $this->addSql("CREATE INDEX IDX_8B40AAC554EA6A5D ON art_object_video (artobject_id)");
        $this->addSql("CREATE INDEX IDX_8B40AAC565B1CCD0 ON art_object_video (tviglevideo_id)");
        $this->addSql("CREATE TABLE art_object_category (artobject_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(artobject_id, category_id))");
        $this->addSql("CREATE INDEX IDX_FC9C9D6C54EA6A5D ON art_object_category (artobject_id)");
        $this->addSql("CREATE INDEX IDX_FC9C9D6C12469DE2 ON art_object_category (category_id)");
        $this->addSql("CREATE TABLE art_object_person (artobject_id INT NOT NULL, person_id INT NOT NULL, PRIMARY KEY(artobject_id, person_id))");
        $this->addSql("CREATE INDEX IDX_EDFD0CDA54EA6A5D ON art_object_person (artobject_id)");
        $this->addSql("CREATE INDEX IDX_EDFD0CDA217BBB47 ON art_object_person (person_id)");
        $this->addSql("CREATE TABLE armd_person_type (id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE TABLE armd_person (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE TABLE person_person_type (person_id INT NOT NULL, persontype_id INT NOT NULL, PRIMARY KEY(person_id, persontype_id))");
        $this->addSql("CREATE INDEX IDX_6BD38C8A217BBB47 ON person_person_type (person_id)");
        $this->addSql("CREATE INDEX IDX_6BD38C8AA0DA5BA6 ON person_person_type (persontype_id)");
        $this->addSql("ALTER TABLE armd_real_museum ADD CONSTRAINT FK_350ECE263DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_real_museum ADD CONSTRAINT FK_350ECE2698260155 FOREIGN KEY (region_id) REFERENCES atlas_region (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_real_museum ADD CONSTRAINT FK_350ECE2612469DE2 FOREIGN KEY (category_id) REFERENCES armd_museum_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_real_museum ADD CONSTRAINT FK_350ECE26129049C2 FOREIGN KEY (atlas_object_id) REFERENCES atlas_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_real_museum_virtual_tour ADD CONSTRAINT FK_AFA07BA492F7C848 FOREIGN KEY (realmuseum_id) REFERENCES armd_real_museum (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE armd_real_museum_virtual_tour ADD CONSTRAINT FK_AFA07BA44B52E5B5 FOREIGN KEY (museum_id) REFERENCES armd_museum (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE exhibit_category ADD CONSTRAINT FK_8E437B81727ACA70 FOREIGN KEY (parent_id) REFERENCES exhibit_category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE art_object ADD CONSTRAINT FK_E712E0101CDA489C FOREIGN KEY (primary_image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE art_object ADD CONSTRAINT FK_E712E0104B52E5B5 FOREIGN KEY (museum_id) REFERENCES armd_real_museum (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE art_object_video ADD CONSTRAINT FK_8B40AAC554EA6A5D FOREIGN KEY (artobject_id) REFERENCES art_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE art_object_video ADD CONSTRAINT FK_8B40AAC565B1CCD0 FOREIGN KEY (tviglevideo_id) REFERENCES tvigle_video (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE art_object_category ADD CONSTRAINT FK_FC9C9D6C54EA6A5D FOREIGN KEY (artobject_id) REFERENCES art_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE art_object_category ADD CONSTRAINT FK_FC9C9D6C12469DE2 FOREIGN KEY (category_id) REFERENCES exhibit_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE art_object_person ADD CONSTRAINT FK_EDFD0CDA54EA6A5D FOREIGN KEY (artobject_id) REFERENCES art_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE art_object_person ADD CONSTRAINT FK_EDFD0CDA217BBB47 FOREIGN KEY (person_id) REFERENCES armd_person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE person_person_type ADD CONSTRAINT FK_6BD38C8A217BBB47 FOREIGN KEY (person_id) REFERENCES armd_person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE person_person_type ADD CONSTRAINT FK_6BD38C8AA0DA5BA6 FOREIGN KEY (persontype_id) REFERENCES armd_person_type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE armd_real_museum_virtual_tour DROP CONSTRAINT FK_AFA07BA492F7C848");
        $this->addSql("ALTER TABLE art_object DROP CONSTRAINT FK_E712E0104B52E5B5");
        $this->addSql("ALTER TABLE exhibit_category DROP CONSTRAINT FK_8E437B81727ACA70");
        $this->addSql("ALTER TABLE art_object_category DROP CONSTRAINT FK_FC9C9D6C12469DE2");
        $this->addSql("ALTER TABLE art_object_video DROP CONSTRAINT FK_8B40AAC554EA6A5D");
        $this->addSql("ALTER TABLE art_object_category DROP CONSTRAINT FK_FC9C9D6C54EA6A5D");
        $this->addSql("ALTER TABLE art_object_person DROP CONSTRAINT FK_EDFD0CDA54EA6A5D");
        $this->addSql("ALTER TABLE person_person_type DROP CONSTRAINT FK_6BD38C8AA0DA5BA6");
        $this->addSql("ALTER TABLE art_object_person DROP CONSTRAINT FK_EDFD0CDA217BBB47");
        $this->addSql("ALTER TABLE person_person_type DROP CONSTRAINT FK_6BD38C8A217BBB47");
        $this->addSql("DROP SEQUENCE armd_real_museum_id_seq");
        $this->addSql("DROP SEQUENCE exhibit_category_id_seq");
        $this->addSql("DROP SEQUENCE art_object_id_seq");
        $this->addSql("DROP SEQUENCE armd_person_type_id_seq");
        $this->addSql("DROP SEQUENCE armd_person_id_seq");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("DROP TABLE armd_real_museum");
        $this->addSql("DROP TABLE armd_real_museum_virtual_tour");
        $this->addSql("DROP TABLE exhibit_category");
        $this->addSql("DROP TABLE art_object");
        $this->addSql("DROP TABLE art_object_video");
        $this->addSql("DROP TABLE art_object_category");
        $this->addSql("DROP TABLE art_object_person");
        $this->addSql("DROP TABLE armd_person_type");
        $this->addSql("DROP TABLE armd_person");
        $this->addSql("DROP TABLE person_person_type");       
    }
}
