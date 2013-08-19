<?php
namespace Damedia\SpecialProjectBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NeighborsCommunicator {
    private $friendlyEntities = array('news'          => array('title' => 'Новость',
                                                               'optgroup' => 'news',
                                                               'entityDescription' => array('class' => 'ArmdNewsBundle:News',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title',

                                                                                            'partedByField' => 'category',
                                                                                            'attributeValue' => 'Новости',

                                                                                            'partitionClass' => 'ArmdNewsBundle:Category',
                                                                                            'partitionIdField' => 'id',
                                                                                            'partitionKeyField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'partedBy',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:news_one_column_list.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/NewsBundle/Resources/views/News/one-column-list.html.twig

                                      'reportage'     => array('title' => 'Репортаж',
                                                               'optgroup' => 'news',
                                                               'entityDescription' => array('class' => 'ArmdNewsBundle:News',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title',

                                                                                            'partedByField' => 'category',
                                                                                            'attributeValue' => 'Репортажи',

                                                                                            'partitionClass' => 'ArmdNewsBundle:Category',
                                                                                            'partitionIdField' => 'id',
                                                                                            'partitionKeyField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'partedBy',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:news_one_column_list.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/NewsBundle/Resources/views/News/one-column-list.html.twig

                                      'announcement'  => array('title' => 'Анонс',
                                                               'optgroup' => 'news',
                                                               'entityDescription' => array('class' => 'ArmdNewsBundle:News',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title',

                                                                                            'partedByField' => 'category',
                                                                                            'attributeValue' => 'Анонсы',

                                                                                            'partitionClass' => 'ArmdNewsBundle:Category',
                                                                                            'partitionIdField' => 'id',
                                                                                            'partitionKeyField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'partedBy',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:news_one_column_list.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/NewsBundle/Resources/views/News/one-column-list.html.twig

                                      'article'       => array('title' => 'Сеатья',
                                                               'optgroup' => 'news',
                                                               'entityDescription' => array('class' => 'ArmdNewsBundle:News',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title',

                                                                                            'partedByField' => 'category',
                                                                                            'attributeValue' => 'Статьи',

                                                                                            'partitionClass' => 'ArmdNewsBundle:Category',
                                                                                            'partitionIdField' => 'id',
                                                                                            'partitionKeyField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'partedBy',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:news_one_column_list.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/NewsBundle/Resources/views/News/one-column-list.html.twig

                                      'theater'       => array('title' => 'Театр',
                                                               'optgroup' => 'theaters',
                                                               'entityDescription' => array('class' => 'ArmdTheaterBundle:Theater',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'simple',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:theater_list_tile.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/TheaterBundle/Resources/views/Default/theater_list_data.html.twig

                                      'performance'   => array('title' => 'Спектакль',
                                                               'optgroup' => 'theaters',
                                                               'entityDescription' => array('class' => 'ArmdPerfomanceBundle:Perfomance',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'simple',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:performance_list.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/PerfomanceBundle/Resources/views/Perfomance/list-content.html.twig

                                      'realMuseum'    => array('title' => 'Музей',
                                                               'optgroup' => 'museums',
                                                               'entityDescription' => array('class' => 'ArmdMuseumBundle:RealMuseum',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'simple',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:museum_list_tile.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/MainBundle/Resources/views/museum_reserve.html.twig [static list!]

                                      'museum'        => array('title' => 'Вирутальный тур',
                                                               'optgroup' => 'museums',
                                                               'entityDescription' => array('class' => 'ArmdMuseumBundle:Museum',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'simple',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:vtour_preview.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/MainBundle/Resources/views/Default/virtual_list.html.twig

                                      'museumLesson'  => array('title' => 'Музейное образование',
                                                               'optgroup' => 'museums',
                                                               'entityDescription' => array('class' => 'ArmdMuseumBundle:Lesson',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'simple',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:lesson_list.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/MainBundle/Resources/views/Lesson/list-content.html.twig

                                      'movie'         => array('title' => 'Кино',
                                                               'optgroup' => 'video',
                                                               'entityDescription' => array('class' => 'ArmdLectureBundle:Lecture',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title',

                                                                                            'partedByField' => 'lectureSuperType',
                                                                                            'attributeValue' => 'LECTURE_SUPER_TYPE_CINEMA',

                                                                                            'partitionClass' => 'ArmdLectureBundle:LectureSuperType',
                                                                                            'partitionIdField' => 'id',
                                                                                            'partitionKeyField' => 'code'),
                                                               'autocompleteListCreateFunction' => 'partedBy',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:movie_list.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/LectureBundle/Resources/views/Default/list_banners.html.twig

                                      'lecture'       => array('title' => 'Лекция',
                                                               'optgroup' => 'video',
                                                               'entityDescription' => array('class' => 'ArmdLectureBundle:Lecture',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title',

                                                                                            'partedByField' => 'lectureSuperType',
                                                                                            'attributeValue' => 'LECTURE_SUPER_TYPE_LECTURE',

                                                                                            'partitionClass' => 'ArmdLectureBundle:LectureSuperType',
                                                                                            'partitionIdField' => 'id',
                                                                                            'partitionKeyField' => 'code'),
                                                               'autocompleteListCreateFunction' => 'partedBy',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:lecture_preview.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/LectureBundle/Resources/views/Default/list_banners.html.twig

                                      'newsVideo'      => array('title' => 'Новостное видео',
                                                                'optgroup' => 'video',
                                                                'entityDescription' => array('class' => 'ArmdLectureBundle:Lecture',
                                                                                             'idField' => 'id',
                                                                                             'titleField' => 'title',

                                                                                             'partedByField' => 'lectureSuperType',
                                                                                             'attributeValue' => 'LECTURE_SUPER_TYPE_NEWS',

                                                                                             'partitionClass' => 'ArmdLectureBundle:LectureSuperType',
                                                                                             'partitionIdField' => 'id',
                                                                                             'partitionKeyField' => 'code'),
                                                                'autocompleteListCreateFunction' => 'partedBy',
                                                                'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:lecture_preview.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/LectureBundle/Resources/views/Default/list_banners.html.twig

                                      'imageOfRussia' => array('title' => 'Образ России',
                                                               'optgroup' => 'objects',
                                                               'entityDescription' => array('class' => 'ArmdAtlasBundle:Object',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title',

                                                                                            'partedByField' => 'primaryCategory',
                                                                                            'attributeValue' => 'Образы России',

                                                                                            'partitionClass' => 'ArmdAtlasBundle:Category',
                                                                                            'partitionIdField' => 'id',
                                                                                            'partitionKeyField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'partedBy',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:imageOfRussia_list_tile.html.twig', 'title' => 'вид по умолчанию'), //from: Armd/AtlasBundle/Resources/views/Default/russia_images_list_tile.html.twig
                                                                                'roundedImage' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:imageOfRussia_roundedImage.html.twig', 'title' => 'круглая картинка'),
                                                                                'tealBackground' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:imageOfRussia_tealBackground.html.twig', 'title' => 'бирюзовый фон'))),

                                      'atlasObject'   => array('title' => 'Объект атласа',
                                                               'optgroup' => 'objects',
                                                               'entityDescription' => array('class' => 'ArmdAtlasBundle:Object',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'simple',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:atlasObject_side.html.twig', 'title' => 'вид по умолчанию'))), //from: Armd/AtlasBundle/Resources/views/Default/object_side.html.twig

                                      'gallery'       => array('title' => 'Галерея',
                                                               'optgroup' => 'others',
                                                               'entityDescription' => array('class' => 'Application\Sonata\MediaBundle\Entity\Gallery',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'name'),
                                                               'autocompleteListCreateFunction' => 'simple',
                                                               'views' => array('default' => array('twig' => 'DamediaSpecialProjectBundle:Neighbors:gallery.html.twig', 'title' => 'вид по умолчанию'))) //from: vendor/sonata-project/media-bundle/Sonata/MediaBundle/Resources/views/Gallery/view.html.twig
                                );

    private $autocompleteListCreate_registeredFunctions;
    private $optgroups = array('objects' => 'Объекты',
                               'museums' => 'Музеи',
                               'theaters' => 'Театры',
                               'news' => 'Новости',
                               'sprojects' => 'Спецпроекты',
                               'blogs' => 'Блоги',
                               'others' => 'Другое',
                               'video' => 'Видео');

    public function __construct() {
        $this->autocompleteListCreate_registeredFunctions = array(
            'simple' => function(EntityManager $em, $entityDescription, $searchPhrase, $limit){
                $json = array();
                $qb = $em->createQueryBuilder();

                $qb->select('n.'.$entityDescription['idField'].' AS id, n.'.$entityDescription['titleField'].' AS title')
                    ->from($entityDescription['class'], 'n')
                    ->where($qb->expr()->like('LOWER(n.'.$entityDescription['titleField'].')', $qb->expr()->literal('%'.$searchPhrase.'%')))
                    ->setMaxResults($limit);
                $result = $qb->getQuery()->getArrayResult();

                foreach ($result as $row) {
                    $json[] = array('value' => $row['id'],
                                    'label' => $row['title']);
                }

                return $json;
            },
            'partedBy' => function(EntityManager $em, $entityDescription, $searchPhrase, $limit){
                $json = array();
                $qb = $em->createQueryBuilder();

                $partition = $em->getRepository($entityDescription['partitionClass'])->findOneBy(array($entityDescription['partitionKeyField'] => $entityDescription['attributeValue']));
                $getterName = 'get'.ucfirst($entityDescription['partitionIdField']);
                $partitionId = $partition->$getterName();

                $qb->select('n.'.$entityDescription['idField'].' AS id, n.'.$entityDescription['titleField'].' AS title')
                    ->from($entityDescription['class'], 'n')
                    ->where($qb->expr()->andX($qb->expr()->like('LOWER(n.'.$entityDescription['titleField'].')', $qb->expr()->literal('%'.$searchPhrase.'%')),
                        $qb->expr()->eq('n.'.$entityDescription['partedByField'], $qb->expr()->literal($partitionId))))
                    ->setMaxResults($limit);
                $result = $qb->getQuery()->getArrayResult();

                foreach ($result as $row) {
                    $json[] = array('value' => $row['id'],
                                    'label' => $row['title']);
                }

                return $json;
            }
        );
    }

    public function getFriendlyEntitiesSelectOptions() {
        $result = array();

        foreach ($this->friendlyEntities as $entityName => $data) {
            $entityOptgroup = $data['optgroup'];
            $optgroupTitle = $this->optgroups[$entityOptgroup];

            if (!isset($result[$optgroupTitle])) {
                $result[$optgroupTitle] = array();
            }

            $result[$optgroupTitle][] = array('name' => $entityName, 'title' => $data['title']);
        }

        return $result;
    }

    public function getFriendlyEntityDescription($entityName) {
        $friendlyEntity = $this->getFriendlyEntity($entityName);

        return $friendlyEntity['entityDescription'];
    }

    public function getFriendlyEntityTwig($entityName, $view) {
        $friendlyEntity = $this->getFriendlyEntity($entityName);

        return $friendlyEntity['views'][$view]['twig'];
    }

    public function createFriendlyEntityAutocompleteList(EntityManager $em, $entityName, $searchPhrase, $limit) {
        $friendlyEntity = $this->getFriendlyEntity($entityName);

        $searchPhrase = strtolower($searchPhrase); //sometimes $searchPhrase could be 'false'... no idea if this cause any problems or not, didn't test it yet
        $createFunctionName = $friendlyEntity['autocompleteListCreateFunction'];
        $createFunction = $this->autocompleteListCreate_registeredFunctions[$createFunctionName];

        return $createFunction($em, $friendlyEntity['entityDescription'], $searchPhrase, $limit);
    }

    public function createFriendlyEntityViewsList($entityName) {
        $friendlyEntity = $this->getFriendlyEntity($entityName);

        return $friendlyEntity['views'];
    }



    private function getFriendlyEntity($entityName) {
        if (!isset($this->friendlyEntities[$entityName])) {
            throw new NotFoundHttpException("Entity '".$entityName."' is not a friendly neighbor!");
        }

        return $this->friendlyEntities[$entityName];
    }
}
?>