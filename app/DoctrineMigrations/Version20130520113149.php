<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130520113149 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
        $this->addSql("ALTER TABLE lecture ADD vertical_banner_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD horizontal_banner_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE lecture ADD CONSTRAINT FK_C16779484D71968E FOREIGN KEY (vertical_banner_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE lecture ADD CONSTRAINT FK_C167794864FFFBF8 FOREIGN KEY (horizontal_banner_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_C16779484D71968E ON lecture (vertical_banner_id)");
        $this->addSql("CREATE INDEX IDX_C167794864FFFBF8 ON lecture (horizontal_banner_id)");
        $this->addSql("ALTER TABLE armd_museum ALTER sort SET  DEFAULT 0");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("ALTER TABLE armd_museum ALTER sort SET  DEFAULT 0");
        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
        $this->addSql("ALTER TABLE lecture DROP vertical_banner_id");
        $this->addSql("ALTER TABLE lecture DROP horizontal_banner_id");
        $this->addSql("ALTER TABLE lecture DROP CONSTRAINT FK_C16779484D71968E");
        $this->addSql("ALTER TABLE lecture DROP CONSTRAINT FK_C167794864FFFBF8");
        $this->addSql("DROP INDEX IDX_C16779484D71968E");
        $this->addSql("DROP INDEX IDX_C167794864FFFBF8");
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
    }
}
