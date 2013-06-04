<?php

namespace Armd\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\FOSUserEvents;
use Armd\UserBundle\Entity\Favorites;

class FavoritesController extends Controller
{
    protected function getEntity($resourceType)
    {
        if ($resourceType == Favorites::TYPE_ATLAS) {
            return 'Armd\AtlasBundle\Entity\Object';
        } elseif ($resourceType == Favorites::TYPE_MEDIA) {
            return 'Armd\NewsBundle\Entity\News';
        } elseif ($resourceType == Favorites::TYPE_LECTURE) {
            return 'Armd\LectureBundle\Entity\Lecture';
        } elseif ($resourceType == Favorites::TYPE_MUSEUM_LESSON) {
            return 'Armd\MuseumBundle\Entity\Lesson';
        } elseif ($resourceType == Favorites::TYPE_PERFORMANCE) {
            return 'Armd\PerfomanceBundle\Entity\Perfomance';
        } elseif ($resourceType == Favorites::TYPE_THEATER) {
            return 'Armd\TheaterBundle\Entity\Theater';
        } elseif ($resourceType == Favorites::TYPE_TOURIST_ROUTE) {
            return 'Armd\TouristRouteBundle\Entity\Route';
        } else {
            return false;
        }
    }
    
    protected function getImageSrc(\Application\Sonata\MediaBundle\Entity\Media $image = null, $format = 'reference')
    {
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
    
    public function indexAction()
    {
        $favorites = array();
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $result = $em->createQuery('SELECT DISTINCT t.resourceType, t.resourceId FROM ArmdUserBundle:Favorites t WHERE t.user = :user ORDER BY t.resourceType')
            ->setParameter('user', $user->getId())
            ->getScalarResult();
        
        foreach ($result as $r) {
            $favoritesIds[$r['resourceType']][] = $r['resourceId'];            
        }
        
        foreach ($favoritesIds as $type => $ids) {
            $entityName = $this->getEntity($type);
            
            if (!$entityName) {
                continue;
            }
            
            $entities = $em->createQuery('SELECT t FROM ' . $entityName . ' t WHERE t.id IN (:ids)')
                ->setParameter('ids', $ids)
                ->getResult();
            
            if ($type == Favorites::TYPE_ATLAS) {
                $favorites[Favorites::TYPE_ATLAS]['title'] = 'Образы России';
                
                foreach ($entities as $e) {
                    $favorites[Favorites::TYPE_ATLAS]['list'][] = array(
                        'image' => $this->getImageSrc($e->getPrimaryImage()),
                        'title' => $e->getTitle(),
                        'url' => $this->get('router')->generate('armd_atlas_default_object_view', array('id' => $e->getId())),
                        'type' => $type,
                        'id' => $e->getId()
                    );
                }
            } elseif ($type == Favorites::TYPE_MEDIA) {               
                foreach ($entities as $e) {
                    if ($e->getCategory()->getSlug()) {
                        $fId = Favorites::TYPE_MEDIA . '-' . $e->getCategory()->getId();
                        $favorites[$fId]['title'] = $e->getCategory()->getTitle();
                        $favorites[$fId]['list'][] = array(
                            'image' => $this->getImageSrc($e->getImage()),
                            'title' => $e->getTitle(),
                            'url' => $this->get('router')->generate('armd_news_item_by_category', array('category' => $e->getCategory()->getSlug(), 'id' => $e->getId())),
                            'type' => $type,
                            'id' => $e->getId()
                        );
                    }
                }
            } elseif ($type == Favorites::TYPE_MUSEUM_LESSON) {
                $favorites[Favorites::TYPE_MUSEUM_LESSON]['title'] = 'Музейное образование';
                
                foreach ($entities as $e) {
                    $favorites[Favorites::TYPE_MUSEUM_LESSON]['list'][] = array(
                        'image' => $this->getImageSrc($e->getImage()),
                        'title' => $e->getTitle(),
                        'url' => $this->get('router')->generate('armd_lesson_item', array('id' => $e->getId())),
                        'type' => $type,
                        'id' => $e->getId()
                    );
                }
            } elseif ($type == Favorites::TYPE_LECTURE) {               
                foreach ($entities as $e) {
                    if ($e->getLectureSuperType()) {
                        $category = $e->getLectureSuperType();
                        $fId = Favorites::TYPE_LECTURE . '-' . $category->getId();
                        $favorites[$fId]['title'] = $category->getName();
                        $favorites[$fId]['list'][] = array(
                            'image' => $this->getImageSrc($e->getMediaLectureVideo()),
                            'title' => $e->getTitle(),
                            'url' => $this->get('router')->generate('armd_lecture_view', array('id' => $e->getId())),
                            'type' => $type,
                            'id' => $e->getId()
                        );
                    }
                }
            } elseif ($type == Favorites::TYPE_THEATER) {
                $favorites[Favorites::TYPE_THEATER]['title'] = 'Театры';
                
                foreach ($entities as $e) {
                    $favorites[Favorites::TYPE_THEATER]['list'][] = array(
                        'image' => $this->getImageSrc($e->getImage()),
                        'title' => $e->getTitle(),
                        'url' => $this->get('router')->generate('armd_theater_item', array('id' => $e->getId())),
                        'type' => $type,
                        'id' => $e->getId()
                    );
                }
            } elseif ($type == Favorites::TYPE_PERFORMANCE) {
                $favorites[Favorites::TYPE_PERFORMANCE]['title'] = 'Спектакли';
                
                foreach ($entities as $e) {
                    $favorites[Favorites::TYPE_PERFORMANCE]['list'][] = array(
                        'image' => $this->getImageSrc($e->getImage()),
                        'title' => $e->getTitle(),
                        'url' => $this->get('router')->generate('armd_perfomance_item', array('id' => $e->getId())),
                        'type' => $type,
                        'id' => $e->getId()
                    );
                }
            } elseif ($type == Favorites::TYPE_TOURIST_ROUTE) {
                $favorites[Favorites::TYPE_TOURIST_ROUTE]['title'] = 'Туристические маршруты';
                
                foreach ($entities as $e) {
                    $favorites[Favorites::TYPE_TOURIST_ROUTE]['list'][] = array(
                        'image' => $this->getImageSrc($e->getPrimaryImage()),
                        'title' => $e->getTitle(),
                        'url' => $this->get('router')->generate('armd_tourist_route_item', array('id' => $e->getId())),
                        'type' => $type,
                        'id' => $e->getId()
                    );
                }
            }
        }
        
        return $this->render('ArmdUserBundle:Favorites:favorites.html.twig', array(
            'favorites' => $favorites
        ));
    }
    
    public function addAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $resourceType = $this->getRequest()->get('type', false);
        $resourceId = $this->getRequest()->get('id', false);
        $entity = $this->getEntity($resourceType);
        
        if (!is_object($user) || !$user instanceof UserInterface || !$resourceType || !$resourceId || !$entity) {
            return new Response('0');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $favorite = $em->getRepository('ArmdUserBundle:Favorites')->findBy(array(
            'user' => $user->getId(),
            'resourceType' => $resourceType,
            'resourceId' => $resourceId
        ));
        
        if ($favorite) {
            return new Response('1');
        }
        
        $favorite = new Favorites();
        $favorite->setUser($user);
        $favorite->setResourceType($resourceType);
        $favorite->setResourceId($resourceId);
        $em->persist($favorite);
        $em->flush();
        
        return new Response('1');
    }

    public function delAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $resourceType = $this->getRequest()->get('type', false);
        $resourceId = $this->getRequest()->get('id', false);
        $entity = $this->getEntity($resourceType);
        
        if (!is_object($user) || !$user instanceof UserInterface || !$resourceType || !$resourceId || !$entity) {
            return new Response('0');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $favorites = $em->getRepository('ArmdUserBundle:Favorites')->findBy(array(
            'user' => $user->getId(),
            'resourceType' => $resourceType,
            'resourceId' => $resourceId
        ));
        
        if ($favorites) {
            foreach ($favorites as $f) {
                $em->remove($f);
            }
            
            $em->flush();                          
        }
        
        return $this->redirect($this->generateUrl('armd_user_favorites'));
    }
}
