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


class MainController extends Controller
{
    public function homepageAction()
    {
        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_main_homepage')
        );

        $em = $this->getDoctrine()->getManager();

        $newRussiaImages = $this->get('armd_atlas.manager.object')->findObjects(
            array(
                ObjectManager::CRITERIA_RUSSIA_IMAGES => true,
                ObjectManager::CRITERIA_ORDER_BY => array('createdAt' => 'DESC'),
                ObjectManager::CRITERIA_LIMIT => 7
            )
        );


        // NewFeature #53983
        $lectures = $em->getRepository('ArmdLectureBundle:Lecture')
            ->findBy(array(
                'id' => array(580,553,592,556),
            ));

        $news = $this->getNewsManager()->findObjects(
            array(
                NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array('news', 'events'),
                NewsManager::CRITERIA_LIMIT => 30
            )
        );

        $lastReportage = $this->getNewsManager()->findObjects(
            array(
                NewsManager::CRITERIA_LIMIT => 1,
                NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array('reportages'),
                NewsManager::CRITERIA_HAS_IMAGE => true
            )
        );
        if (!empty($lastReportage)) {
            $lastReportage = $lastReportage[0];
        }

        $lastInterview = $this->getNewsManager()->findObjects(
            array(
                NewsManager::CRITERIA_LIMIT => 1,
                NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array('interviews'),
                NewsManager::CRITERIA_HAS_IMAGE => true
            )
        );
        if (!empty($lastInterview)) {
            $lastInterview = $lastInterview[0];
        }

        $museum = $this->getDoctrine()->getManager()->find('ArmdMuseumBundle:Museum', 28);

        return $this->render(
            'ArmdMainBundle:Homepage:homepage.html.twig',
            array(
                'news' => $news,
                'newRussiaImages' => $newRussiaImages,
                'newVideos' => $lectures,
                'lastReportage' => $lastReportage,
                'lastInterview' => $lastInterview,
                'museum' => $museum
            )
        );
    }

    public function loginLinksAction()
    {
        $response = $this->render(
            'ArmdMainBundle:Main:login_links.html.twig'
        );
//        $response->setSharedMaxAge(0);
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
            ->getRepository('ArmdBannerBundle:Banner')
            ->getBanner($bannerCode);

        return $this->render('ArmdMainBundle:Main:background_banner.html.twig', array('banner' => $banner));
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
    
    public function peopleCulturePollsAction()
    {   
        $apiModule = 'm_vote';
        $apiCount = 1;        
        $request = $this->getRequest();
        $request->query->add(array(
            'restUrl' => '/ru/export/',
            'method' => 'get',
            'params' => array(
                'module' => $apiModule,
                'count' => $apiCount
            )
        ));
        $response = $this->container->get('armd_main.ajax_proxy')->ajaxRequest(
            $this->container->getParameter('communication_platform_domain'),
            $request
        );
        
        return $this->render('ArmdMainBundle:Homepage:people_culture_polls.html.twig', 
                array(
                    'userLogged' => $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') ? 1 : 0,
                    'data' => $response->getContent(),
                    'apiModule' => $apiModule,
                    'apiCount' => $apiCount
                ));
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
