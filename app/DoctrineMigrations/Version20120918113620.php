<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120918113620 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");
        
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ADD odnoklassniki_uid VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE fos_user_user ADD vkontakte_uid VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE fos_user_user DROP ok_uid");
        $this->addSql("ALTER TABLE fos_user_user DROP vk_uid");
        $this->addSql("ALTER TABLE fos_user_user DROP fb_uid");
        $this->addSql("ALTER TABLE fos_user_user DROP tw_uid");
        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");
        
        $this->addSql("ALTER TABLE fos_user_user ADD ok_uid VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE fos_user_user ADD vk_uid VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE fos_user_user ADD fb_uid VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE fos_user_user ADD tw_uid VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE fos_user_user DROP odnoklassniki_uid");
        $this->addSql("ALTER TABLE fos_user_user DROP vkontakte_uid");
        $this->addSql("ALTER TABLE fos_user_user ALTER facebook_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER twitter_data TYPE TEXT");
        $this->addSql("ALTER TABLE fos_user_user ALTER gplus_data TYPE TEXT");
        $this->addSql("ALTER TABLE media__media ALTER provider_metadata TYPE TEXT");
    }
}
