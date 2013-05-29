<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class Version20130408083835 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE subscription_issue_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE subscription_mailing_list_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE user_mailing_list (user_id INT NOT NULL, mailing_list_id INT NOT NULL, PRIMARY KEY(user_id, mailing_list_id))");
        $this->addSql("CREATE INDEX IDX_95080FADA76ED395 ON user_mailing_list (user_id)");
        $this->addSql("CREATE INDEX IDX_95080FAD2C7EF3E4 ON user_mailing_list (mailing_list_id)");
        $this->addSql("CREATE TABLE subscription_issue (id INT NOT NULL, mailing_list_id INT NOT NULL, content TEXT NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, sendedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_E895224D2C7EF3E4 ON subscription_issue (mailing_list_id)");
        $this->addSql("CREATE TABLE subscription_mailing_list (id INT NOT NULL, periodically BOOLEAN NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, issueSignature TEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("ALTER TABLE user_mailing_list ADD CONSTRAINT FK_95080FADA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE user_mailing_list ADD CONSTRAINT FK_95080FAD2C7EF3E4 FOREIGN KEY (mailing_list_id) REFERENCES subscription_mailing_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE subscription_issue ADD CONSTRAINT FK_E895224D2C7EF3E4 FOREIGN KEY (mailing_list_id) REFERENCES subscription_mailing_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE user_mailing_list DROP CONSTRAINT FK_95080FAD2C7EF3E4");
        $this->addSql("ALTER TABLE subscription_issue DROP CONSTRAINT FK_E895224D2C7EF3E4");
        $this->addSql("DROP SEQUENCE subscription_issue_id_seq");
        $this->addSql("DROP SEQUENCE subscription_mailing_list_id_seq");
        $this->addSql("CREATE SEQUENCE acl_classes_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_object_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE acl_security_identities_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("DROP TABLE user_mailing_list");
        $this->addSql("DROP TABLE subscription_issue");
        $this->addSql("DROP TABLE subscription_mailing_list");
    }
}
