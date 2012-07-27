<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\NewsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Armd\ResourceBundle\Entity\News;

class LoadNewsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @return array
     */
    public function getNewsDefinition()
    {
        $result = array();
        $newsText = 'Ортзанд охлаждает тензиометр, однозначно свидетельствуя о неустойчивости процесса в целом.
            Удобрение, как следствие уникальности почвообразования в данных условиях, химически охлаждает трог только в
            отсутствие тепло- и массообмена с окружающей средой. Подзолообразование статистически слагает
            фирновый огненный пояс, однозначно свидетельствуя о неустойчивости процесса в целом.
            Эвапотранспирация стягивает магматический монолит, за счет чего увеличивается мощность коры под
            многими хребтами. Большое значение для формирования химического состава грунтовых и пластовых вод имеет
            неорганическое соединение притягивает ортштейн, причем, вероятно, быстрее, чем прочность мантийного вещества.';
        $arrayNewsData = array(
            'Песчаный агробиогеоценоз: гипотеза и теории',
            'Поглотительный грязевой вулкан — актуальная национальная задача',
            'Грубообломочный латерит глазами современников',
            'Уорд Каннингем и его соавтор Бо Леуф',
            'В книге The Wiki Way: Quick Collaboration on the Web',
            'Вики предлагает всем пользователям редактировать любую страницу',
            'Вики не является тщательно изготовленным сайтом для случайных посетителей',
            'Песчаный агробиогеоценоз: гипотеза и теории',
            'Поглотительный грязевой вулкан — актуальная национальная задача',
            'Грубообломочный латерит глазами современников',
            'Уорд Каннингем и его соавтор Бо Леуф',
            'В книге The Wiki Way: Quick Collaboration on the Web',
            'Вики предлагает всем пользователям редактировать любую страницу',
            'Вики не является тщательно изготовленным сайтом для случайных посетителей',
            'Песчаный агробиогеоценоз: гипотеза и теории',
            'Поглотительный грязевой вулкан — актуальная национальная задача',
            'Грубообломочный латерит глазами современников',
            'Уорд Каннингем и его соавтор Бо Леуф',
            'В книге The Wiki Way: Quick Collaboration on the Web',
            'Вики предлагает всем пользователям редактировать любую страницу',
            'Вики не является тщательно изготовленным сайтом для случайных посетителей'
        );
        $arrayStreamData = array(
            $this->getReference('stream-news-main'),
            $this->getReference('stream-news-news')
        );
        foreach($arrayNewsData as $title) {
            $result[] = array(
                'title'     => $title,
                'text'      => $newsText,
                'stream'    => $arrayStreamData[rand(0,1)],
                'announce'  => 'Ортзанд охлаждает тензиометр, однозначно свидетельствуя о неустойчивости процесса в целом.',
                'date'      => new \DateTime(),
            );
        }
        return $result;
    }

    public function load(ObjectManager $manager)
    {
        foreach($this->getNewsDefinition() as $newsDesc) {
            $news = new News();
            $news->setTitle($newsDesc['title']);
            $news->setAnnounce($newsDesc['announce']);
            $news->setBody($newsDesc['text']);
            $news->setDate($newsDesc['date']);
            $news->setStream( $newsDesc['stream'] );
            $manager->persist($news);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 102;
    }
}
