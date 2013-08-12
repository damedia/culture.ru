<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Armd\AtlasBundle\Entity\ObjectManager; //this is only for rendering 'imageOfRussia' snippet and must be moved to 'NeighborsCommunicator' class

class RenderController extends Controller {
	public function footerMenuElementsAction() {
		$pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
    	$pages = $pageRepository->findBy(array('parent' => null, 'isPublished' => true), array('id' => 'ASC'));
    	
    	$menuElements = array();
    	foreach ($pages as $page) {
    		$menuElements[] = array('href' => $this->generateUrl('damedia_special_project_view', array('slug' => $page->getSlug())), 'caption' => $page->getTitle());
    	}
		
		return $this->render('DamediaSpecialProjectBundle:Default:footerMenu.html.twig', array('ProjectsFooterMenu' => $menuElements));
	}
	
	public function indexAction() {
        $helper = $this->get('special_project_helper');
        $breadcrumbs = $helper->createInitialBreadcrumbsArray($this);

        $pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
        $pages = $pageRepository->findBy(array('isPublished' => true, 'parent' => null), array('id' => 'DESC'));

        $projects = array();
        foreach ($pages as $page) {
            $projects[] = array('href' => $this->generateUrl('damedia_special_project_view', array('slug' => $page->getSlug())),
                                'caption' => $page->getTitle(),
                                'padding' => 0);

            $children = $page->getChildren();
            if (count($children) > 0) {
                foreach ($children as $child) {
                    $projects[] = array('href' => $this->generateUrl('damedia_special_project_view', array('slug' => $child->getSlug())),
                        'caption' => $child->getTitle(),
                        'padding' => 1);
                }
            }
        }

		return $this->render('DamediaSpecialProjectBundle:Default:index.html.twig', array('PageTitle' => 'Спецпроекты',
                                                                                          'Breadcrumbs' => $breadcrumbs,
                                                                                          'Projects' => $projects));
	}
	
    public function viewAction($slug) {
    	$pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
        $page = $pageRepository->findOneBy(array('slug' => $slug, 'isPublished' => true));
        $helper = $this->get('special_project_helper');

        return $helper->renderSpecialProjectPage($this, $page, 'Страница <span class="variable">'.$slug.'</span> не опубликована или не существует!');
    }

    public function snippetAction($entity, $itemId) {
        $communicator = $this->get('special_project_neighbors_communicator');
        $entityDescr = $communicator->getFriendlyEntity($entity);

        $object = $this->getDoctrine()->getRepository($entityDescr['class'])->find($itemId);

        if (!$object) {
            return $this->render('DamediaSpecialProjectBundle:Neighbors:notExists.html.twig', array('entity' => $entity, 'itemId' => $itemId));
        }

        //This switch is an EVIL CREATURE and must be moved into 'NeighborsCommunicator' class!!!
        switch ($entity) {
            case 'news': //Новость
                /**
                 * Copy templates from: Armd/NewsBundle/Resources/views/News/...
                 *      one-column-list.html.twig               <- DONE
                 */

                return $this->render('DamediaSpecialProjectBundle:Neighbors:news_one_column_list.html.twig', array('object' => $object));
                break;

            case 'theater': //Театр
                /**
                 * Copy templates from: Armd/TheaterBundle/Resources/views/Default/...
                 *      theater_list_data.html.twig             <- DONE
                 */

                return $this->render('DamediaSpecialProjectBundle:Neighbors:theater_list_tile.html.twig', array('object' => $object));
                break;

            case 'realMuseum': //Музей
                /**
                 * Real museums list is HARDCODED in a twig inside the MainBundle... Type of these museums is probably 'музей'.
                 * Real museums with other types (which are: 'музей-усадьба' and 'музей-заповедник') are inside the MuseumBundle.
                 *
                 * Copy templates from: Armd/MainBundle/Resources/views/...
                 *      museum_reserve.html.twig [static list!] <- DONE
                 *
                 *                      Armd/MuseumBundle/Recources/views/...
                 *
                 *      .................                       <-
                 */

                return $this->render('DamediaSpecialProjectBundle:Neighbors:museum_list_tile.html.twig', array('object' => $object));
                break;

            case 'museum': //Вирутальный тур
                /**
                 * Copy templates from: Armd/MainBundle/Resources/views/Default/...
                 *      virtual_list.html.twig                  <- DONE
                 *      virtual_list_text.html.twig             <-
                 */

                return $this->render('DamediaSpecialProjectBundle:Neighbors:vtour_preview.html.twig', array('object' => $object));
                break;

            case 'lecture': //Лекция
                /**
                 * Copy templates from: Armd/LectureBundle/Resources/views/Default/...
                 *      list_banners.html.twig                  <- DONE
                 */

                return $this->render('DamediaSpecialProjectBundle:Neighbors:lecture_preview.html.twig', array('object' => $object));
                break;

            case 'imageOfRussia': //Образ России
                /**
                 * Copy templates from: Armd/AtlasBundle/Resources/views/Default/...
                 *      russia_images_list_full.html.twig       <-
                 *      russia_images_list_short.html.twig      <-
                 *      russia_images_list_special.html.twig    <-
                 *      russia_images_list_tile.html.twig       <- DONE
                 */

                return $this->render('DamediaSpecialProjectBundle:Neighbors:imageOfRussia_list_tile.html.twig', array('object' => $object));
                break;

            case 'gallery': //Галерея
                /**
                 * Copy templates from: vendor/sonata-project/media-bundle/Sonata/MediaBundle/Resources/views/Gallery/...
                 *      view.html.twig                          <- DONE
                 */

                return $this->render('DamediaSpecialProjectBundle:Neighbors:gallery.html.twig', array('object' => $object));
                break;

            default:
                return $this->render('DamediaSpecialProjectBundle:Neighbors:notExists.html.twig', array('entity' => $entity, 'itemId' => $itemId));
        }
    }
}