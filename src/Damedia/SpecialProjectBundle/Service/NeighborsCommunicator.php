<?php
namespace Damedia\SpecialProjectBundle\Service;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NeighborsCommunicator {
    public function getFriendlyEntity($entityName) {
        $friends = array('news'          => array('class' => 'ArmdNewsBundle:News',
                                                  'idField' => 'id',
                                                  'titleField' => 'title'),
                         'museum'        => array('class' => 'ArmdMuseumBundle:Museum',
                                                  'idField' => 'id',
                                                  'titleField' => 'title'),
                         'realMuseum'    => array('class' => 'ArmdMuseumBundle:RealMuseum',
                                                  'idField' => 'id',
                                                  'titleField' => 'title'),
                         'lecture'       => array('class' => 'ArmdLectureBundle:Lecture',
                                                  'idField' => 'id',
                                                  'titleField' => 'title'),
                         'artObject'     => array('class' => 'ArmdExhibitBundle:ArtObject',
                                                  'idField' => 'id',
                                                  'titleField' => 'title'),
                         'theater'       => array('class' => 'ArmdTheaterBundle:Theater',
                                                  'idField' => 'id',
                                                  'titleField' => 'title'),
                         'image'         => array('class' => 'Application\Sonata\MediaBundle\Entity\Media',
                                                  'idField' => 'id',
                                                  'nameField' => 'name',
                                                  'contextField' => 'context',
                                                  'updatedAtField' => 'updatedAt'),
                         'imageOfRussia' => array('class' => 'ArmdAtlasBundle:Object',
                                                  'idField' => 'id',
                                                  'titleField' => 'title',
                                                  'primaryCategoryField' => 'primaryCategory',
                                                  'categoryClass' => 'ArmdAtlasBundle:Category',
                                                  'categoryId' => 'id',
                                                  'categoryTitle' => 'title')
        );

        if (!isset($friends[$entityName])) {
            throw new NotFoundHttpException("Entity not found");
        }

        return $friends[$entityName];
    }
}
?>