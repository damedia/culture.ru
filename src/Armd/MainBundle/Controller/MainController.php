<?php

namespace Armd\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\ListBundle\Controller\ListController;
use Armd\ListBundle\Repository\BaseRepository;
use Armd\AtlasBundle\Manager\ObjectManager;


class MainController extends Controller
{
    public function homepageAction()
    {
        $categories = $this->getNewsManager()->getCategories();

        $newRussiaImages = $this->get('armd_atlas.manager.object')->findObjects(
            array(
                ObjectManager::CRITERIA_RUSSIA_IMAGES => true,
                ObjectManager::CRITERIA_ORDER_BY => array('createdAt' => 'DESC'),
                ObjectManager::CRITERIA_LIMIT => 7
            )
        );


        return $this->render(
            'ArmdMainBundle:Homepage:homepage.html.twig',
            array(
                'news' => $this->getNews($categories),
                'categories' => $categories,
                'newRussiaImages' => $newRussiaImages
            )
        );
    }

    public function bannerAction()
    {
        return $this->renderTemplate('banner');
    }

    public function aboutAction()
    {
        return $this->renderTemplate('about');
    }

    public function servicesAction()
    {
        return $this->renderTemplate('services');
    }
    public function libraryAction()
    {
        return $this->renderTemplate('library');
    }
    public function bannersAction()
    {
        return $this->renderTemplate('banners');
    }
	public function printAction()
    {
        return $this->renderTemplate('print');
    }

    public function randomRussiaImagesAction()
    {
        return $this->render(
            'ArmdMainBundle:Main:randomRussiaImages.html.twig',
            array(
                'russiaImages' => $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object')->findRandomRussiaImages(
                    6
                )
            )
        );
    }

    public function latestTopicsAction()
    {
        $domain = $this->container->getParameter('communication_platform_domain');
        $topics = array(
            '/export/?module=m_ep_propostal',
            '/export/?module=m_ep_forum',
        );

        $result = array();
        foreach ($topics as $url) {
            $content = @file_get_contents("http://{$domain}{$url}");
            if ($content !== false) {
                $obj = json_decode($content);
                $result[] = $obj->{'data'};
            }
        }

        return $this->render(
            'ArmdMainBundle:Communication:index.html.twig',
            array(
                'topics' => $result,
                'domain' => $domain,
            )
        );
    }

    public function pressArchiveAction($format = 'gallery_archive', $category = 'archive')
    {
        $criteria = array(
            'defaultFormat' => $format,
        );

        return $this->render(
            'ArmdMainBundle:Archive:index.html.twig',
            array(
                'category' => $category,
                'gallery' => $this->getGalleryManager()->findOneBy($criteria)
            )
        );
    }

    public function communicationPlatformRequestAction()
    {
        return $this->container->get('armd_main.ajax_proxy')->ajaxRequest(
            $this->container->getParameter('communication_platform_domain'),
            $this->getRequest()
        );
    }

    function getNews(array $categories)
    {
        $result = array();

        foreach ($categories as $c) {
            $result[$c->getSlug()] = $this->get('armd_news.manager.news')->getPager(
                array('category' => $c->getSlug()),
                1,
                45
            );
        }

        return $result;
//        $categorySlugs = array();
//        foreach($categories as $category) {
//            $categorySlugs[] = $category->getSlug();
//        }
//
//        return $this->get('armd_news.controller.news')->getPaginator(
//                        array('category' => $categorySlugs),
//                        1,
//                        45
//                    );
    }

    function getEvents($limit)
    {
        $events = $this->getNewsManager()->getPager(
            array(
                'category' => 'events',
                'has_image' => true,
            ),
            1,
            $limit
        );
        return $events;
    }


    function renderTemplate($template, $parameters = array())
    {
        return $this->render("ArmdMainBundle::{$template}.html.twig", $parameters);
    }

    function getNewsManager()
    {
        return $this->get('armd_news.manager.news');
    }


    function getGalleryManager()
    {
        return $this->get('sonata.media.manager.gallery');
    }

    function getTemplateName($action)
    {
        return "{$this->getControllerName()}:{$action}.html.twig";

    }

    function getControllerName()
    {
        return 'ArmdMainBundle:Main';
    }


}
