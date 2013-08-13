<?php
namespace Damedia\SpecialProjectBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NeighborsCommunicator {
    private $friendlyEntities = array('news'          => array('text' => 'Новость',
                                                               'entityDescription' => array('class' => 'ArmdNewsBundle:News',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'default',
                                                               'defaultSnippetTwig' => 'news_one_column_list.html.twig'), //from: Armd/NewsBundle/Resources/views/News/one-column-list.html.twig

                                      'theater'       => array('text' => 'Театр',
                                                               'entityDescription' => array('class' => 'ArmdTheaterBundle:Theater',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'default',
                                                               'defaultSnippetTwig' => 'theater_list_tile.html.twig'), //from: Armd/TheaterBundle/Resources/views/Default/theater_list_data.html.twig

                                      'realMuseum'    => array('text' => 'Музей',
                                                               'entityDescription' => array('class' => 'ArmdMuseumBundle:RealMuseum',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'default',
                                                               'defaultSnippetTwig' => 'museum_list_tile.html.twig'), //from: Armd/MainBundle/Resources/views/museum_reserve.html.twig [static list!]

                                      'museum'        => array('text' => 'Вирутальный тур',
                                                               'entityDescription' => array('class' => 'ArmdMuseumBundle:Museum',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'default',
                                                               'defaultSnippetTwig' => 'vtour_preview.html.twig'), //from: Armd/MainBundle/Resources/views/Default/virtual_list.html.twig

                                      'lecture'       => array('text' => 'Лекция',
                                                               'entityDescription' => array('class' => 'ArmdLectureBundle:Lecture',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title'),
                                                               'autocompleteListCreateFunction' => 'default',
                                                               'defaultSnippetTwig' => 'lecture_preview.html.twig'), //from: Armd/LectureBundle/Resources/views/Default/list_banners.html.twig

                                      'imageOfRussia' => array('text' => 'Образ России',
                                                               'entityDescription' => array('class' => 'ArmdAtlasBundle:Object',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'title',
                                                                                            'primaryCategoryField' => 'primaryCategory',
                                                                                            'categoryClass' => 'ArmdAtlasBundle:Category',
                                                                                            'categoryId' => 'id',
                                                                                            'categoryTitle' => 'title'),
                                                               'autocompleteListCreateFunction' => 'imageOfRussia',
                                                               'defaultSnippetTwig' => 'imageOfRussia_list_tile.html.twig'), //from: Armd/AtlasBundle/Resources/views/Default/russia_images_list_tile.html.twig

                                      'gallery'       => array('text' => 'Галерея',
                                                               'entityDescription' => array('class' => 'Application\Sonata\MediaBundle\Entity\Gallery',
                                                                                            'idField' => 'id',
                                                                                            'titleField' => 'name'),
                                                               'autocompleteListCreateFunction' => 'default',
                                                               'defaultSnippetTwig' => 'gallery.html.twig') //from: vendor/sonata-project/media-bundle/Sonata/MediaBundle/Resources/views/Gallery/view.html.twig
                                );

    private $autocompleteListCreate_registeredFunctions;

    public function __construct() {
        $this->autocompleteListCreate_registeredFunctions = array(
            'default' => function(EntityManager $em, $entityDescription, $searchPhrase, $limit){
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
            'imageOfRussia' => function(EntityManager $em, $entityDescription, $searchPhrase, $limit){
                $json = array();
                $qb = $em->createQueryBuilder();

                $imageOfRussiaCategory = $em->getRepository($entityDescription['categoryClass'])->findOneBy(array($entityDescription['categoryTitle'] => 'Образы России'));
                $getterName = 'get'.ucfirst($entityDescription['categoryId']);
                $imageOfRussiaCategoryId = $imageOfRussiaCategory->$getterName();

                $qb->select('n.'.$entityDescription['idField'].' AS id, n.'.$entityDescription['titleField'].' AS title')
                    ->from($entityDescription['class'], 'n')
                    ->where($qb->expr()->andX($qb->expr()->like('LOWER(n.'.$entityDescription['titleField'].')', $qb->expr()->literal('%'.$searchPhrase.'%')),
                                              $qb->expr()->eq('n.'.$entityDescription['primaryCategoryField'], $qb->expr()->literal($imageOfRussiaCategoryId))))
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

    public function getFriendlyEntitiesJsonList() {
        $result = array();

        foreach ($this->friendlyEntities as $name => $data) {
            $result[] = array('value' => $name, 'text' => $data['text']);
        }

        return $result;
    }

    public function getFriendlyEntityDescription($entityName) {
        if (!isset($this->friendlyEntities[$entityName])) {
            throw new NotFoundHttpException("Entity '".$entityName."' is not a friendly neighbor!");
        }

        return $this->friendlyEntities[$entityName]['entityDescription'];
    }

    public function getFriendlyEntityDefaultTwig($entityName) {
        $entityDescription = $this->getFriendlyEntityDescription($entityName);

        return $entityDescription['defaultSnippetTwig'];
    }

    public function createFriendlyEntityAutocompleteList(EntityManager $em, $entityName, $searchPhrase, $limit) {
        $entityDescription = $this->getFriendlyEntityDescription($entityName);

        $searchPhrase = strtolower($searchPhrase);
        $createFunctionName = $entityDescription['autocompleteListCreateFunction'];
        $createFunction = $this->autocompleteListCreate_registeredFunctions[$createFunctionName];

        return $createFunction($em, $entityDescription, $searchPhrase, $limit);
    }
}
?>