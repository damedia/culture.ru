<?php
namespace Armd\MainBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        if ($request->getLocale() == 'en') {

            $mainMenu = $menu->addChild('menu.homepage', array('route' => 'armd_main_homepage'));

            $menu->addChild('menu.atlas',            array('route' => 'armd_atlas_index'));
            $menu->addChild('menu.russia_images',    array('route' => 'armd_atlas_russia_images'));
            $menu->addChild('menu.virtual_museums',  array('route' => 'armd_museum_index'));

            //--- Events
            $eventsMenu = $menu->addChild('menu.news_index', array('route' => 'armd_news_list_index'));
            //--- /Events

            //--- Information (About)
            $infoMenu = $menu->addChild(
                'menu.about',
                array(
                    'route' => 'armd_main_about'
                )
            );

        } else {

            //--- Main
            $mainMenu = $menu->addChild(
                'menu.homepage',
                array(
                    'route' => 'armd_main_homepage'
                )
            );

            $mainMenu->addChild(
                'menu.russia_images',
                array(
                    'route' => 'armd_atlas_russia_images'
                )
            );

            $mainMenu->addChild(
                'menu.virtual_museums',
                array(
                    'route' => 'armd_museum_index'
                )
            );

            $mainMenu->addChild(
                'menu.atlas',
                array(
                    'route' => 'armd_atlas_index'
                )
            );

            $mainMenu->addChild(
                'menu.events_on_map',
                array(
                    'route' => 'armd_news_map',
                )
            );

            $mainMenu->addChild(
                'menu.chronicle',
                array(
                    'route' => 'armd_chronicle_index'
                )
            );

            $mainMenu->addChild(
                'menu.spiritual_traditions',
                array(
                    'route' => 'armd_main_project_traditions'
                )
            );

            //--- /Main


            //--- Events
            $eventsMenu = $menu->addChild(
                'menu.news_index',
                array(
                    'route' => 'armd_news_list_index',
                )
            );

            $eventsMenu->addChild(
                'menu.news',
                array(
                    'route' => 'armd_news_list_index_by_category',
                    'routeParameters' => array('category' => 'news')
                )
            );

            $eventsMenu->addChild(
                'menu.events',
                array(
                    'route' => 'armd_news_list_index_by_category',
                    'routeParameters' => array('category' => 'events')
                )
            );

            $eventsMenu->addChild(
                'menu.reportage',
                array(
                    'route' => 'armd_news_list_index_by_category',
                    'routeParameters' => array('category' => 'reportages')
                )
            );

            $eventsMenu->addChild(
                'menu.interview',
                array(
                    'route' => 'armd_news_list_index_by_category',
                    'routeParameters' => array('category' => 'interviews')
                )
            );

            $eventsMenu->addChild(
                'menu.online_translation',
                array(
                    'route' => 'armd_online_translation',
                )
            );

            $eventsMenu->addChild(
                'menu.culture_magazine',
                array(
                    'route' => 'armd_paper_edition',
                    'routeParameters' => array('slug' => 'archive')
                )
            );

            $eventsMenu->addChild(
                'menu.literature_magazine',
                array(
                    'route' => 'armd_paper_edition',
                    'routeParameters' => array('slug' => 'litnews')
                )
            );

//            $eventsMenu->addChild(
//                'menu.online_translation',
//                array(
//                    'route' => 'armd_main_online_translation'
//                )
//            );

            //--- /Events


            //--- Video
            $videoMenu = $menu->addChild(
                'menu.video',
                array(
                    'route' => 'armd_lecture_default_list',
                    'routeParameters' => array('supertype' => 3)
                )
            );

            $videoMenu->addChild(
                'menu.cinema',
                array(
                    'route' => 'armd_lecture_default_list',
                    'routeParameters' => array('supertype' => 3)
                )
            );

            $videoMenu->addChild(
                'menu.lectures',
                array(
                    'route' => 'armd_lecture_default_list',
                    'routeParameters' => array('supertype' => 1)
                )
            );

            $videoMenu->addChild(
                'menu.translations',
                array(
                    'route' => 'armd_lecture_default_list',
                    'routeParameters' => array('supertype' => 2)
                )
            );

            $videoMenu->addChild(
                'menu.top100',
                array(
                    'route' => 'armd_lecture_default_list',
                    'routeParameters' => array('supertype' => 4)
                )
            );


            //--- /Video


            //--- Communication

            $communicationMenu = $menu->addChild(
                'menu.communication',
                array(
                    'uri' => 'http://people.culture.ru/opengov/expert/'
                )
            );

            $communicationMenu->addChild(
                'menu.forum',
                array(
                    'uri' => 'http://people.culture.ru/forum/'
                )
            );

            $communicationMenu->addChild(
                'menu.government_services',
                array(
                    'route' => 'armd_main_services'
                )
            );

            $communicationMenu->addChild(
                'menu.open_government',
                array(
                    'uri' => 'http://people.culture.ru/opengov/expert/'
                )
            );

            $communicationMenu->addChild(
                'menu.social_council',
                array(
                    'uri' => 'http://people.culture.ru/community_council/'
                )
            );

            $communicationMenu->addChild(
                'menu.government_control',
                array(
                    'uri' => 'http://people.culture.ru/forum_private/'
                )
            );

            //--- /Communication


            //--- Kids
            $kidsMenu = $menu->addChild(
                'menu.kids',
                array(
                    'uri' => '/kids/children.html'
                )
            );

            //--- /Kids


            //--- Special
            $specialMenu = $menu->addChild(
                'menu.special_projects',
                array(
                    'route' => 'armd_main_project_1150'
                )
            );

            $specialMenu->addChild(
                'menu.special_projects_1150',
                array(
                    'route' => 'armd_main_project_1150'
                )
            );

            $specialMenu->addChild(
                'menu.special_projects_romanov450',
                array(
                    'route' => 'armd_news_item_by_category',
                    'routeParameters' => array('category' => 'news', 'id' => 955)
                )
            );

            $specialMenu->addChild(
                'menu.borodino',
                array(
                    'route' => 'armd_main_project_brodino'
                )
            );

            $specialMenu->addChild(
                'menu.special_projects_stanislavski',
                array(
                    'route' => 'armd_news_item_by_category',
                    'routeParameters' => array('category' => 'news', 'id' => 3724)
                )
            );
            //--- /Special

        }

        $menu->setCurrentUri($request->getRequestUri());

        return $menu;
    }

}