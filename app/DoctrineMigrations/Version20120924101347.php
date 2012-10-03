<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120924101347 extends AbstractMigration
{
    private $museums = array(
        array(
        'link' => '/tretyakov_gallery/tretyakov_museum.html',
        'img' => '/tretyakovka.jpg',
        'description' => 'Государственная Третьяковская галерея'
        ),
        array(
        'link' => '/vostok_museum/vostok_museum.html',
        'img' => '/vostoka.jpg',
        'description' => 'Государственный музей искусства народов Востока'
        ),
        array(
        'link' => '/bahrushina/bahrushina_museum.html',
        'img' => '/01.jpg',
        'description' => 'Театральный музей имени А.А. Бахрушина'
        ),
        array(
        'link' => '/borodino/borodino_museum.html',
        'img' => '/borodinskaya.jpg',
        'description' => 'Музей-панорама &laquo;Бородинская битва&raquo;'
        ),
        array(
        'link' => '/kulikovo/kulikovo_museum.html',
        'img' => '/kulikovo.jpg',
        'description' => 'Государственный военно-исторический и природный<br>музей-заповедник &laquo;Куликово поле&raquo;'
        ),
        array(
        'link' => '/glinka_museum/glinka_museum.html',
        'img' => '/03.jpg',
        'description' => 'Всероссийское музейное объединение музыкальной культуры имени М.И. Глинки'
        ),
        array(
        'link' => '/radishev/radishev_museum.html',
        'img' => '/radisheva.jpg',
        'description' => 'Саратовский государственный художественный музей <br>имени А.Н. Радищева'
        ),
        array(
        'link' => '/mordovia/mordovia_museum.html',
        'img' => '/saransk.jpg',
        'description' => 'Мордовский республиканский музей <br>изобразительных искусств имени С.Д. Эрьзи'
        ),
        array(
        'link' => '/sklif2/sheremetieva-media-trest.html',
        'img' => '/sklif.jpg',
        'description' => 'Странноприимный дом<br>Н.П. Шереметьева'
        ),
        array(
        'link' => '/sholohov/sholohov.html',
        'img' => '/sholohov.jpg',
        'description' => 'Государственный музей-заповедник М.А. Шолохова'
        ),
        array(
        'link' => '/polyana/iasnaya_polyana.html',
        'img' => '/yasnaya_polyana.jpg',
        'description' => 'Музей-усадьба Л.Н. Толстого<br> «Ясная Поляна»'
        ),
        array(
        'link' => '/kalashnikova/kalashnikova.html',
        'img' => '/kalash.jpg',
        'description' => 'Музей стрелкового оружия им. Михаила Калашникова'
        ),
        array(
        'link' => '/pushkin/pushkin.html',
        'img' => '/pushkin1.jpg',
        'description' => 'Мемориальный Музей-квартира А.С.Пушкина'
        ),
        array(
        'link' => '/dekabristy/dekabristy.html',
        'img' => '/dekabristy.jpg',
        'description' => 'Музей  Памяти декабристов'
        ),
        array(
        'link' => '/monastyr_suzdal/monastery_suzdal.html',
        'img' => '/suzdal.jpg',
        'description' => 'Владимиро-Суздальский музей-заповедник'
        ),
        array(
        'link' => '/ryazan/ryazan.html',
        'img' => '/ryazan.jpg',
        'description' => 'Рязанский историко-архитектурный музей-заповедник'
        ),
        array(
        'link' => '/korely/malye_korely.html',
        'img' => '/malye_korely.jpg',
        'description' => 'Архангельский государственный музей деревянного зодчества<br>  и народного искусства «Малые Корелы»'
        ),
        array(
        'link' => '/tsarskoe_selo/tsarskoe_selo.html',
        'img' => '/tzarskoe_selo.jpg',
        'description' => 'Государственный музей-заповедник «Царское Село»'
        ),
        array(
        'link' => '/schusev/schusev.html',
        'img' => '/schuseva.jpg',
        'description' => 'Государственный музей архитектуры <br>имени А.В. Щусева'
        ),
        array(
        'link' => '/etnomuseum/ethnomuseum.html',
        'img' => '/etnomuseum.jpg',
        'description' => 'Российский этнографический музей'
        ),
        array(
        'link' => '/novgorod/novgorod.html',
        'img' => '/novgorod.jpg',
        'description' => 'Новгородский государственный объединенный<br>музей-заповедник'
        ),
        array(
        'link' => '/tarhany/tarhany.html',
        'img' => '/tarhany.jpg',
        'description' => 'Государственный Лермонтовский музей-заповедник «Тарханы»'
        ),
        array(
        'link' => '/varvarka/varvarka.html',
        'img' => '/varvarka.jpg',
        'description' => 'Старый английский двор на Варварке'
        ),    
    );

    public function up(Schema $schema)
    {
        $this->addSql("CREATE SEQUENCE armd_museum_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE armd_museum (id INT NOT NULL, image_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, body TEXT DEFAULT NULL, url VARCHAR(255) NOT NULL, published BOOLEAN NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_F7F419893DA5256D ON armd_museum (image_id)");
        $this->addSql("ALTER TABLE armd_museum ADD CONSTRAINT FK_F7F419893DA5256D FOREIGN KEY (image_id) REFERENCES media__media (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        
        foreach ($this->museums as $m) {
            $this->addSql("INSERT INTO armd_museum (id, title, url, body, published) VALUES (nextval('armd_museum_id_seq'), :description, :link, :img, false)", $m);    
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
