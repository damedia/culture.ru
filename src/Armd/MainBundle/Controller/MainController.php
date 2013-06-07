<?php

namespace Armd\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Armd\NewsBundle\Entity\NewsManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\ListBundle\Controller\ListController;
use Armd\ListBundle\Repository\BaseRepository;
use Armd\AtlasBundle\Entity\ObjectManager;
use Armd\MuseumBundle\Entity\MuseumManager;


class MainController extends Controller
{
    public function homepageAction()
    {
        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_main_homepage')
        );

        $em = $this->getDoctrine()->getManager();

//        $newRussiaImages = $this->get('armd_atlas.manager.object')->findObjects(
//            array(
//                ObjectManager::CRITERIA_RUSSIA_IMAGES => true,
//                ObjectManager::CRITERIA_ORDER_BY => array('showOnMain' => 'DESC', 'showOnMainOrd' => 'ASC', 'createdAt' => 'DESC'),
//                ObjectManager::CRITERIA_LIMIT => 7
//            )
//        );

        $lectures = $em->getRepository('ArmdLectureBundle:Lecture')->findBy(
            array(
                'showOnMain' => true,
            ),
            array(
                'showOnMainOrd' => 'ASC'
            )
        );

        if ($this->getRequest()->getLocale() === 'en') {
            $newsCount = 3;
        } else {
            $newsCount = 16;
            $activeTranslation = $this->get('armd_online_translation.manager.online_translation')
                                 ->getActiveTranslation();
            if (!empty($activeTranslation)) {
                $newsCount -= 9;
            }
        }
        $news = $this->getNewsManager()->findObjects(
            array(
                NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array('news', 'events'),
                NewsManager::CRITERIA_LIMIT => $newsCount,
            )
        );

//        $lastReportage = $this->getNewsManager()->findObjects(
//            array(
//                NewsManager::CRITERIA_LIMIT => 1,
//                NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array('reportages'),
//                NewsManager::CRITERIA_ORDER_BY => array('showOnMain' => 'DESC', 'showOnMainOrd' => 'ASC'),
//                NewsManager::CRITERIA_HAS_IMAGE => true
//            )
//        );
//        if (!empty($lastReportage)) {
//            $lastReportage = $lastReportage[0];
//        }
//
//        $lastInterview = $this->getNewsManager()->findObjects(
//            array(
//                NewsManager::CRITERIA_LIMIT => 1,
//                NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array('interviews'),
//                NewsManager::CRITERIA_ORDER_BY => array('showOnMain' => 'DESC', 'showOnMainOrd' => 'ASC'),
//                NewsManager::CRITERIA_HAS_IMAGE => true
//            )
//        );
//        if (!empty($lastInterview)) {
//            $lastInterview = $lastInterview[0];
//        }

        $museums =  $this->getMuseumManager()->findObjects(
            array(
                MuseumManager::CRITERIA_LIMIT => 1,
                NewsManager::CRITERIA_ORDER_BY => array('showOnMain' => 'DESC', 'showOnMainOrd' => 'ASC')
            )
        );
        $museum = $museums[0];
        // */
        /*
        $museum = $em->getRepository('ArmdMuseumBundle:Museum')->findBy(
            array(
                'showOnMain' => true,
            ),
            array(
                'showOnMainOrd' => 'ASC'
            )
        );
        */
        $response = $this->render(
            'ArmdMainBundle:Homepage:homepage.html.twig',
            array(
                'news' => $news,
//                'newRussiaImages' => $newRussiaImages,
                'newVideos' => $lectures,
//                'lastReportage' => $lastReportage,
//                'lastInterview' => $lastInterview,
                'museum' => $museum
            )
        );
//        $response->setPublic();
//        $response->setSharedMaxAge(120);

        return $response;
    }

    public function loginLinksAction()
    {
        $response = $this->render(
            'ArmdMainBundle:Main:login_links.html.twig'
        );

        return $response;
    }

    public function backgroundBannerAction()
    {
        if ($this->getRequest()->getLocale() == 'en') {
            $bannerCode = 'HEAD_BANNER_EN';
        } else {
            $bannerCode = 'HEAD_BANNER';
        }

        $banner = $this->getDoctrine()->getManager()
            ->getRepository('ArmdExtendedBannerBundle:BaseBanner')
            ->getBanner($bannerCode);

        return $this->render('ArmdMainBundle:Main:background_banner.html.twig', array('banner' => $banner));
    }
    
    public function underconstructionAction()
    {
        return $this->renderTemplate('underconstruction');
    }

    public function contactsAction()
    {
        return $this->renderTemplate('contacts');
    }

    public function bannerAction()
    {
        return $this->renderTemplate('banner');
    }

    public function aboutAction()
    {
        return $this->renderTemplate('about');
    }

    public function museumReserveAction()
    {
        return $this->renderTemplate('museum_reserve');
    }

    public function may9Action()
    {
        return $this->renderTemplate('may9');
    }

    public function may9_filmsAction()
    {
        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_main_may9')
        );
        return $this->renderTemplate('may9_films');
    }

    public function may9_dayAction()
    {
        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_main_may9')
        );
        return $this->renderTemplate('may9_day');
    }

    public function servicesAction($id = null)
    {
      if (empty($id)) {
          return $this->renderTemplate('services');
      } else {
          return $this->render("ArmdMainBundle:Services:service_$id.html.twig");
      }
    }

    public function libraryAction()
    {
        return $this->renderTemplate('library');
    }

    public function bannersAction()
    {
        return $this->renderTemplate('banners');
    }

    public function onlineTranslationAction()
    {
        return $this->renderTemplate('online_translation');
    }

	public function printAction()
    {
        return $this->renderTemplate('print');
    }

    public function intermuseumAction()
    {
        return $this->renderTemplate('intermuseum');
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
            'ArmdMainBundle:Homepage:latest_communication_topics.html.twig',
            array(
                'topics' => $result,
                'domain' => $domain,
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

        $newsManager = $this->get('armd_news.manager.news');
        foreach ($categories as $c) {
            $result[$c->getSlug()] = $newsManager->findObjects(array(
                    NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array($c->getSlug()),
                    NewsManager::CRITERIA_LIMIT => 25
                ));
        }

//        foreach ($categories as $c) {
//            $result[$c->getSlug()] = $this->get('armd_news.manager.news')->getPager(
//                array('category' => $c->getSlug()),
//                1,
//                25
//            );
//        }
//
        return $result;
    }

    function renderTemplate($template, $parameters = array())
    {
        return $this->render("ArmdMainBundle::{$template}.html.twig", $parameters);
    }

    /**
     * @return \Armd\NewsBundle\Entity\NewsManager
     */
    function getNewsManager()
    {
        return $this->get('armd_news.manager.news');
    }

    /**
     * @return \Armd\NewsBundle\Entity\NewsManager
     */
    function getMuseumManager()
    {
        return $this->get('armd_museum.manager.news');
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

    public function specialProjectsRomanov450Action()
    {
        return $this->render('ArmdMainBundle::special_projects_romanov450.html.twig');
    }

    public function specialProjectsStanislavskiAction()
    {
        return $this->render('ArmdMainBundle::special_projects_stanislavski150.html.twig');
    }

}
