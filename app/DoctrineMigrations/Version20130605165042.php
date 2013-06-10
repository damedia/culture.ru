<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130605165042 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "
                INSERT INTO oauth_client (id, random_id, redirect_uris, secret, allowed_grant_types)
                VALUES(:id, :random_id, :redirect_uris, :secret, :allowed_grant_types)
            ",
            array(
                'id' => 2,
                'random_id' => 'progorod-mobile',
                'redirect_uris' => 'a:0:{}',
                'secret' => '1f7f9ecf1fda91aa7b23a',
                'allowed_grant_types' => 'a:2:{i:0;s:8:"password";i:1;s:13:"refresh_token";}'
            )
        );

        $this->addSql(
            "
                INSERT INTO oauth_client (id, random_id, redirect_uris, secret, allowed_grant_types)
                VALUES(:id, :random_id, :redirect_uris, :secret, :allowed_grant_types)
            ",
            array(
                'id' => 3,
                'random_id' => '3tcijceqovswggc4k84o8kwcswkgogk8s40s8kwwgoscswswsc',
                'redirect_uris' => 'a:1:{i:0;s:21:"http://www.google.com";}',
                'secret' => '5nvtgg4bqgg8scsg08k8sk4w448sgc0o4o8ks0g4o4cwg0gk0k',
                'allowed_grant_types' => 'a:2:{i:0;s:8:"password";i:1;s:13:"refresh_token";}'
            )
        );

        $this->addSql("ALTER SEQUENCE oauth_client_id_seq RESTART WITH 4");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
