<?php

namespace Armd\UserBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\UserBundle\Entity\Favorites;
use Application\Sonata\MediaBundle\Entity\Media;

class FavoritesController extends Controller {
    protected function getImageSrc(Media $image = null, $format = 'reference') {
        if (!$image) {
            return '';
        }
        
        $mediaPool = $this->get('sonata.media.pool');
        $provider = $mediaPool->getProvider($image->getProviderName());
        
        if ($format != 'reference') {
            $format = $provider->getFormatName($image, $format);       
        }
        
        return $provider->generatePublicUrl($image, $format);
    }
    
    public function indexAction() {
        $favorites = array();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $router = $this->get('router');
        
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $em = $this->getDoctrine()->getManager();

        //TODO: ugly query!
        $result = $em
            ->createQuery('
                SELECT DISTINCT t.resourceType, t.resourceId
                FROM ArmdUserBundle:Favorites t
                WHERE t.user = :user
                ORDER BY t.resourceType')
            ->setParameter('user', $user->getId())
            ->getScalarResult();
        
        foreach ($result as $r) {
            $favoritesIds[$r['resourceType']][] = $r['resourceId'];            
        }

        if (!empty($favoritesIds)) {
            foreach ($favoritesIds as $type => $ids) {
                switch ($type) {
                    case Favorites::TYPE_ATLAS:
                        $entityName = 'Armd\AtlasBundle\Entity\Object';
                        break;
                    case Favorites::TYPE_MEDIA:
                        $entityName = 'Armd\NewsBundle\Entity\News';
                        break;
                    case Favorites::TYPE_LECTURE:
                        $entityName = 'Armd\LectureBundle\Entity\Lecture';
                        break;
                    case Favorites::TYPE_MUSEUM_LESSON:
                        $entityName = 'Armd\MuseumBundle\Entity\Lesson';
                        break;
                    case Favorites::TYPE_PERFORMANCE:
                        $entityName = 'Armd\PerfomanceBundle\Entity\Perfomance';
                        break;
                    case Favorites::TYPE_THEATER:
                        $entityName = 'Armd\TheaterBundle\Entity\Theater';
                        break;
                    case Favorites::TYPE_TOURIST_ROUTE:
                        $entityName = 'Armd\TouristRouteBundle\Entity\Route';
                        break;
                    default:
                        $entityName = false;
                }

                if (!$entityName) {
                    continue;
                }

                //TODO: ugly query!
                $entities = $em
                    ->createQuery('
                        SELECT t
                        FROM '.$entityName.' t
                        WHERE t.id IN (:ids)')
                    ->setParameter('ids', $ids)
                    ->getResult();

                if ($type == Favorites::TYPE_ATLAS) {
                    $favorites[Favorites::TYPE_ATLAS]['title'] = 'Образы России';

                    foreach ($entities as $e) {
                        $favorites[Favorites::TYPE_ATLAS]['list'][] = array(
                            'image' => $this->getImageSrc($e->getPrimaryImage()),
                            'title' => $e->getTitle(),
                            'url' => $router->generate('armd_atlas_default_object_view', array('id' => $e->getId())),
                            'type' => $type,
                            'id' => $e->getId()
                        );
                    }
                }
                elseif ($type == Favorites::TYPE_MEDIA) {
                    foreach ($entities as $e) {
                        if ($e->getCategory()->getSlug()) {
                            $fId = Favorites::TYPE_MEDIA . '-' . $e->getCategory()->getId();
                            $favorites[$fId]['title'] = $e->getCategory()->getTitle();
                            $favorites[$fId]['list'][] = array(
                                'image' => $this->getImageSrc($e->getImage()),
                                'title' => $e->getTitle(),
                                'url' => $router->generate('armd_news_item_by_category', array('category' => $e->getCategory()->getSlug(), 'id' => $e->getId())),
                                'type' => $type,
                                'id' => $e->getId()
                            );
                        }
                    }
                }
                elseif ($type == Favorites::TYPE_MUSEUM_LESSON) {
                    $favorites[Favorites::TYPE_MUSEUM_LESSON]['title'] = 'Музейное образование';

                    foreach ($entities as $e) {
                        $favorites[Favorites::TYPE_MUSEUM_LESSON]['list'][] = array(
                            'image' => $this->getImageSrc($e->getImage()),
                            'title' => $e->getTitle(),
                            'url' => $router->generate('armd_lesson_item', array('id' => $e->getId())),
                            'type' => $type,
                            'id' => $e->getId()
                        );
                    }
                }
                elseif ($type == Favorites::TYPE_LECTURE) {
                    foreach ($entities as $e) {
                        if ($e->getLectureSuperType()) {
                            $category = $e->getLectureSuperType();
                            $fId = Favorites::TYPE_LECTURE . '-' . $category->getId();
                            $favorites[$fId]['title'] = $category->getName();
                            $favorites[$fId]['list'][] = array(
                                'image' => $this->getImageSrc($e->getMediaLectureVideo()),
                                'title' => $e->getTitle(),
                                'url' => $router->generate('armd_lecture_view', array('id' => $e->getId())),
                                'type' => $type,
                                'id' => $e->getId()
                            );
                        }
                    }
                }
                elseif ($type == Favorites::TYPE_THEATER) {
                    $favorites[Favorites::TYPE_THEATER]['title'] = 'Театры';

                    foreach ($entities as $e) {
                        $favorites[Favorites::TYPE_THEATER]['list'][] = array(
                            'image' => $this->getImageSrc($e->getImage()),
                            'title' => $e->getTitle(),
                            'url' => $router->generate('armd_theater_item', array('id' => $e->getId())),
                            'type' => $type,
                            'id' => $e->getId()
                        );
                    }
                }
                elseif ($type == Favorites::TYPE_PERFORMANCE) {
                    $favorites[Favorites::TYPE_PERFORMANCE]['title'] = 'Спектакли';

                    foreach ($entities as $e) {
                        $favorites[Favorites::TYPE_PERFORMANCE]['list'][] = array(
                            'image' => $this->getImageSrc($e->getImage()),
                            'title' => $e->getTitle(),
                            'url' => $router->generate('armd_perfomance_item', array('id' => $e->getId())),
                            'type' => $type,
                            'id' => $e->getId()
                        );
                    }
                }
                elseif ($type == Favorites::TYPE_TOURIST_ROUTE) {
                    $favorites[Favorites::TYPE_TOURIST_ROUTE]['title'] = 'Туристические маршруты';

                    foreach ($entities as $e) {
                        $favorites[Favorites::TYPE_TOURIST_ROUTE]['list'][] = array(
                            'image' => $this->getImageSrc($e->getPrimaryImage()),
                            'title' => $e->getTitle(),
                            'url' => $router->generate('armd_tourist_route_item', array('id' => $e->getId())),
                            'type' => $type,
                            'id' => $e->getId()
                        );
                    }
                }
            }
        }
        else {
            $favorites = array();
        }

        return $this->render('ArmdUserBundle:Favorites:favorites.html.twig', array('favorites' => $favorites));
    }
    
    public function addAction() {
        $request = $this->getRequest();

        $entityType = $request->get('type', false);
        $entityId = $request->get('id', false);

        if (!$entityType || !$entityId) {
            return new Response('0');
        }

        $favoritesManager = $this->get('armd_favorites_manager');
        $result = $favoritesManager->addToFavorites($entityType, $entityId);

        return $result ? new Response('1') : new Response('0');
    }

    public function delAction() {
        $request = $this->getRequest();

        $entityType = $request->get('type', false);
        $entityId = $request->get('id', false);
        $doRedirect = $request->get('redirect', 'true');

        if (!$entityType || !$entityId) {
            return new Response('0');
        }

        $favoritesManager = $this->get('armd_favorites_manager');
        $result = $favoritesManager->removeFromFavorites($entityType, $entityId);

        if (!$result) {
            return new Response('0');
        }

        return ($doRedirect == 'true') ? $this->redirect($this->generateUrl('armd_user_favorites')) : new Response('1');
    }
}
