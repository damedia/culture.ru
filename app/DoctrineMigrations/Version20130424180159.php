<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130424180159 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE tourist_route_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE tourist_route_point_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE tourist_route_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE tourist_route (id INT NOT NULL, banner_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, published BOOLEAN NOT NULL, title VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, type VARCHAR(10) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_A130300F684EC833 ON tourist_route (banner_id)");
        $this->addSql("CREATE INDEX IDX_A130300FDE12AB56 ON tourist_route (created_by)");
        $this->addSql("CREATE INDEX IDX_A130300F16FE72E1 ON tourist_route (updated_by)");
        $this->addSql("CREATE TABLE tourist_route__tourist_route_category (route_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(route_id, category_id))");
        $this->addSql("CREATE INDEX IDX_3062741B34ECB4E6 ON tourist_route__tourist_route_category (route_id)");
        $this->addSql("CREATE INDEX IDX_3062741B12469DE2 ON tourist_route__tourist_route_category (category_id)");
        $this->addSql("CREATE TABLE tourist_route__media_image (route_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(route_id, media_id))");
        $this->addSql("CREATE INDEX IDX_836BD97B34ECB4E6 ON tourist_route__media_image (route_id)");
        $this->addSql("CREATE INDEX IDX_836BD97BEA9FDD75 ON tourist_route__media_image (media_id)");
        $this->addSql("CREATE TABLE tourist_route__tvigle_video (route_id INT NOT NULL, tviglevideo_id INT NOT NULL, PRIMARY KEY(route_id, tviglevideo_id))");
        $this->addSql("CREATE INDEX IDX_C6354EF434ECB4E6 ON tourist_route__tvigle_video (route_id)");
        $this->addSql("CREATE INDEX IDX_C6354EF465B1CCD0 ON tourist_route__tvigle_video (tviglevideo_id)");
        $this->addSql("CREATE TABLE tourist_route__atlas_region (route_id INT NOT NULL, region_id INT NOT NULL, PRIMARY KEY(route_id, region_id))");
        $this->addSql("CREATE INDEX IDX_CFC4C14A34ECB4E6 ON tourist_route__atlas_region (route_id)");
        $this->addSql("CREATE INDEX IDX_CFC4C14A98260155 ON tourist_route__atlas_region (region_id)");
        $this->addSql("CREATE TABLE tourist_route__atlas_object (route_id INT NOT NULL, object_id INT NOT NULL, PRIMARY KEY(route_id, object_id))");
        $this->addSql("CREATE INDEX IDX_680B9BD034ECB4E6 ON tourist_route__atlas_object (route_id)");
        $this->addSql("CREATE INDEX IDX_680B9BD0232D562B ON tourist_route__atlas_object (object_id)");
        $this->addSql("CREATE TABLE tourist_route__tourist_route_point (route_id INT NOT NULL, point_id INT NOT NULL, PRIMARY KEY(route_id, point_id))");
        $this->addSql("CREATE INDEX IDX_1B50AC9634ECB4E6 ON tourist_route__tourist_route_point (route_id)");
        $this->addSql("CREATE INDEX IDX_1B50AC96C028CEA2 ON tourist_route__tourist_route_point (point_id)");
        $this->addSql("CREATE TABLE tourist_route__tourist_route (route_id INT NOT NULL, route2_id INT NOT NULL, PRIMARY KEY(route_id, route2_id))");
        $this->addSql("CREATE INDEX IDX_C96DBFDF34ECB4E6 ON tourist_route__tourist_route (route_id)");
        $this->addSql("CREATE INDEX IDX_C96DBFDF8D842984 ON tourist_route__tourist_route (route2_id)");
        $this->addSql("CREATE TABLE tourist_route_point (id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, list_order INT NOT NULL, show BOOLEAN NOT NULL, lat NUMERIC(15, 10) DEFAULT NULL, lon NUMERIC(15, 10) DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE TABLE tourist_route_category (id INT NOT NULL, icon_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_8DBA71454B9D732 ON tourist_route_category (icon_id)");
        $this->addSql("ALTER TABLE tourist_route ADD CONSTRAINT FK_A130300F684EC833 FOREIGN KEY (banner_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route ADD CONSTRAINT FK_A130300FDE12AB56 FOREIGN KEY (created_by) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route ADD CONSTRAINT FK_A130300F16FE72E1 FOREIGN KEY (updated_by) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__tourist_route_category ADD CONSTRAINT FK_3062741B34ECB4E6 FOREIGN KEY (route_id) REFERENCES tourist_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__tourist_route_category ADD CONSTRAINT FK_3062741B12469DE2 FOREIGN KEY (category_id) REFERENCES tourist_route_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__media_image ADD CONSTRAINT FK_836BD97B34ECB4E6 FOREIGN KEY (route_id) REFERENCES tourist_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__media_image ADD CONSTRAINT FK_836BD97BEA9FDD75 FOREIGN KEY (media_id) REFERENCES media__media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__tvigle_video ADD CONSTRAINT FK_C6354EF434ECB4E6 FOREIGN KEY (route_id) REFERENCES tourist_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__tvigle_video ADD CONSTRAINT FK_C6354EF465B1CCD0 FOREIGN KEY (tviglevideo_id) REFERENCES tvigle_video (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__atlas_region ADD CONSTRAINT FK_CFC4C14A34ECB4E6 FOREIGN KEY (route_id) REFERENCES tourist_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__atlas_region ADD CONSTRAINT FK_CFC4C14A98260155 FOREIGN KEY (region_id) REFERENCES atlas_region (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__atlas_object ADD CONSTRAINT FK_680B9BD034ECB4E6 FOREIGN KEY (route_id) REFERENCES tourist_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__atlas_object ADD CONSTRAINT FK_680B9BD0232D562B FOREIGN KEY (object_id) REFERENCES atlas_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__tourist_route_point ADD CONSTRAINT FK_1B50AC9634ECB4E6 FOREIGN KEY (route_id) REFERENCES tourist_route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__tourist_route_point ADD CONSTRAINT FK_1B50AC96C028CEA2 FOREIGN KEY (point_id) REFERENCES tourist_route_point (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__tourist_route ADD CONSTRAINT FK_C96DBFDF34ECB4E6 FOREIGN KEY (route_id) REFERENCES tourist_route (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route__tourist_route ADD CONSTRAINT FK_C96DBFDF8D842984 FOREIGN KEY (route2_id) REFERENCES tourist_route (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE tourist_route_category ADD CONSTRAINT FK_8DBA71454B9D732 FOREIGN KEY (icon_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE tourist_route__tourist_route_category DROP CONSTRAINT FK_3062741B34ECB4E6");
        $this->addSql("ALTER TABLE tourist_route__media_image DROP CONSTRAINT FK_836BD97B34ECB4E6");
        $this->addSql("ALTER TABLE tourist_route__tvigle_video DROP CONSTRAINT FK_C6354EF434ECB4E6");
        $this->addSql("ALTER TABLE tourist_route__atlas_region DROP CONSTRAINT FK_CFC4C14A34ECB4E6");
        $this->addSql("ALTER TABLE tourist_route__atlas_object DROP CONSTRAINT FK_680B9BD034ECB4E6");
        $this->addSql("ALTER TABLE tourist_route__tourist_route_point DROP CONSTRAINT FK_1B50AC9634ECB4E6");
        $this->addSql("ALTER TABLE tourist_route__tourist_route DROP CONSTRAINT FK_C96DBFDF34ECB4E6");
        $this->addSql("ALTER TABLE tourist_route__tourist_route DROP CONSTRAINT FK_C96DBFDF8D842984");
        $this->addSql("ALTER TABLE tourist_route__tourist_route_point DROP CONSTRAINT FK_1B50AC96C028CEA2");
        $this->addSql("ALTER TABLE tourist_route__tourist_route_category DROP CONSTRAINT FK_3062741B12469DE2");
        $this->addSql("DROP SEQUENCE tourist_route_id_seq");
        $this->addSql("DROP SEQUENCE tourist_route_point_id_seq");
        $this->addSql("DROP SEQUENCE tourist_route_category_id_seq");
        $this->addSql("DROP TABLE tourist_route");
        $this->addSql("DROP TABLE tourist_route__tourist_route_category");
        $this->addSql("DROP TABLE tourist_route__media_image");
        $this->addSql("DROP TABLE tourist_route__tvigle_video");
        $this->addSql("DROP TABLE tourist_route__atlas_region");
        $this->addSql("DROP TABLE tourist_route__atlas_object");
        $this->addSql("DROP TABLE tourist_route__tourist_route_point");
        $this->addSql("DROP TABLE tourist_route__tourist_route");
        $this->addSql("DROP TABLE tourist_route_point");
        $this->addSql("DROP TABLE tourist_route_category");
    }
}
