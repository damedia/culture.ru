<?php
namespace Armd\AtlasBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Armd\AtlasBundle\Entity\Category;
use Symfony\Component\Yaml\Parser;


class LoadTestCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        // create root category
        $rootCategory = new Category();
        $rootCategory->setTitle('== Корневая категория ==');
        $manager->persist($rootCategory);

        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_categories.yml'));

        foreach ($data['categories'] as $categoryData) {
            $this->saveCategory($manager, $categoryData, $rootCategory);
        }
        $manager->flush();
    }

    function saveCategory(ObjectManager $manager, array $categoryData, Category $parentCategory)
    {
        $category = new Category();
        $category->setParent($parentCategory);
        $category->setTitle($categoryData['title']);
        $category->setDescription('Механизм сочленений начинает сюжетный парафраз, и это является некими межсловесными отношениями другого типа, природу которых еще предстоит конкретизировать далее. Если архаический миф не знал противопоставления реальности тексту, эвокация уязвима. Эти слова совершенно справедливы, однако стихотворение интегрирует дискурс и передается в этом стихотворении Донна метафорическим образом циркуля. Цикл, по определению просветляет символ, таким образом в некоторых случаях образуются рефрены, кольцевые композиции, анафоры. Декодирование, не учитывая количества слогов, стоящих между ударениями, существенно приводит палимпсест, и это придает ему свое звучание, свой характер. Вопрос о популярности произведений того или иного автора относится к сфере культурологии, однако диалогичность нивелирует амфибрахий, особенно подробно рассмотрены трудности, с которыми сталкивалась женщина-крестьянка в 19 веке. ');

        if (!empty($categoryData['icon'])) {
            $category->setIconMedia($this->createMediaImage($categoryData['icon']));
        }

        if (!empty($categoryData['ref_code'])) {
            $this->addReference($categoryData['ref_code'], $category);
        }

        $manager->persist($category);

        if (!empty($categoryData['children'])) {
            foreach ($categoryData['children'] as $childrenCategoryData) {
                $this->saveCategory($manager, $childrenCategoryData, $category);
            }
        }

    }

    public static function createMediaImage($fileName) {
        $file = self::createUploadedFile($fileName);
        $media = new Media();
        $media->setBinaryContent($file);
        $media->setContext(static::getContext());
        $media->setProviderName('sonata.media.provider.image');
        return $media;
    }

    public static function createUploadedFile($fileName)
    {
        $filePath = __DIR__ . '/../../../Resources/fixtures/category_icons/' . $fileName;
        if(!file_exists($filePath)) {
            throw new \InvalidArgumentException('Media file not found ' . $fileName);
        }

        $file = new UploadedFile($filePath, $fileName);
        return $file;
    }

    public static function getContext()
    {
        return 'atlas_icon';
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 120;
    }
}