<?php
namespace Armd\MainBundle\Menu;

use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    private $factory;

    private $em;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        if ($request->getLocale() == 'en') {

            $mainMenu = $menu->addChild('menu.homepage', array('route' => 'armd_main_homepage'));

            $menu->addChild('menu.atlas',            array('route' => 'armd_atlas_index'));
            $menu->addChild('menu.russia_images',    array('route' => 'armd_atlas_russia_images'));

            $museumMenu = $menu->addChild('menu.virtual_museums',  array('route' => 'armd_museum_virtual'));
            $museumMenu->addChild('menu.virtual_museums',  array('route' => 'armd_museum_virtual'));
            $museumMenu->addChild('menu.war_gallery',  array('route' => 'armd_war_gallery'));

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
            /*
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
            
//            $mainMenu->addChild(
//                'menu.art_objects',
//                array(
//                    'route' => 'armd_exhibit_list'
//                )
//            );

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
                'menu.may9',
                array(
                    'route' => 'armd_main_may9'
                )
            );

            $mainMenu->addChild(
                'menu.spiritual_traditions',
                array(
                    'route' => 'armd_main_project_traditions'
                )
            );

            $mainMenu->addChild(
                'menu.tourist_routes',
                array(
                    'route' => 'armd_tourist_route_list'
                )
            );

//            $mainMenu->addChild(
//                'menu.theaters',
//                array(
//                    'route' => 'armd_theater_list'
//                )
//            );

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

//            $eventsMenu->addChild(
//                'menu.online_translation',
//                array(
//                    'route' => 'armd_online_translation',
//                )
//            );

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
            */
            
            //--- News
            $newsMenu = $menu->addChild(
                'menu.news',
                array(
                    'route' => 'armd_news_list_index_by_category',
                    'routeParameters' => array('category' => 'news')
                )
            );
            //--- /News

            //--- Media

            $mediaMenu = $menu->addChild(
                'menu.media',
                array(
                    'route' => 'armd_news_list_index_by_category',
                    'routeParameters' => array('category' => 'reportages')
                )
            );

            $mediaMenu->addChild(
                'menu.reportage',
                array(
                    'route' => 'armd_news_list_index_by_category',
                    'routeParameters' => array('category' => 'reportages')
                )
            );

            $mediaMenu->addChild(
                'menu.interview',
                array(
                    'route' => 'armd_news_list_index_by_category',
                    'routeParameters' => array('category' => 'interviews')
                )
            );
            
            $mediaMenu->addChild(
                'menu.article',
                array(
                    'route' => 'armd_news_list_index_by_category',
                    'routeParameters' => array('category' => 'articles')
                )
            );

            $mediaMenu->addChild(
                'menu.events',
                array(
                    'route' => 'armd_news_list_index_by_category',
                    'routeParameters' => array('category' => 'events')
                )
            );
            
            $mediaMenu->addChild(
                'menu.lectures_news',
                array(
                    'route' => 'armd_lecture_news_index',
                )
            );

            //--- /Media

            //--- russiaimages
            $russiaimagesMenu = $menu->addChild(
                'menu.russia_images',
                array(
                    'route' => 'armd_atlas_russia_images'
                )
            );
            //--- /russiaimages

            //--- Museums
            $museumMenu = $menu->addChild(
                'menu.museum',
                array(
                    'route' => 'armd_museum_virtual'
                )
            );
            
            $museumMenu->addChild(
                'menu.virtual_museum',
                array(
                    'route' => 'armd_museum_virtual'
                )
            );  

//            $museumMenu->addChild(
//                'menu.museum_guide',
//                array(
//                    'route' => 'armd_museum_guide_index'
//                )
//            );

            $museumMenu->addChild(
                'menu.museum_lesson',
                array(
                    'route' => 'armd_lesson_list'
                )
            );
            
            $museumMenu->addChild(
                'menu.museum_reserve',
                array(
                    'route' => 'armd_main_museum_reserve'
                )
            );

            $museumMenu->addChild(
                'menu.galley_of_war',
                array(
                    'route' => 'armd_war_gallery'
                )
            );
            //--- /Museums


            //--- Cinema
            $cinemaMenu = $menu->addChild(
                'menu.cinema',
                array(
                    'route' => 'armd_lecture_cinema_index',
                    'routeParameters' => array('genreSlug' => 'feature-film')
                )
            );

            $this->addCinemaMenuItems($cinemaMenu);
            //--- /Cinema

            //--- Theatre

            $theatreMenu = $menu->addChild(
                'menu.theatre',
                array(
                    'route' => 'armd_theater_list',
                )
            );

            $theatreMenu->addChild(
                'menu.perfomance',
                array(
                    'route' => 'armd_perfomance_list'
                )
            );
            
            //--- /Theatre

            //--- Music

            $musicMenu = $menu->addChild(
                'menu.music',
                array(
                    'route' => 'armd_main_underconstruction',
                )
            );
            
            //--- /Music

            //--- Lectures
            $lectureMenu = $menu->addChild(
                'menu.lectures',
                array(
                    'route' => 'armd_lecture_lecture_index'
                )
            );
            //--- /Lectures

            //--- atlas
            $atlasMenu = $menu->addChild(
                'menu.atlas',
                array(
                    'route' => 'armd_atlas_index'
                )
            );
            
            //--- /atlas

//            //--- Kids
//            $kidsMenu = $menu->addChild(
//                'menu.kids',
//                array(
//                    'uri' => '/kids/children.html'
//                )
//            );
//
//            //--- /Kids

            
            //--- Communication

//            $communicationMenu = $menu->addChild(
//                'menu.communication',
//                array(
//                    'uri' => 'http://people.culture.ru/forum/'
//                )
//            );
//
//            $communicationMenu->addChild(
//                'menu.forum',
//                array(
//                    'uri' => 'http://people.culture.ru/forum/'
//                )
//            );
//
//            $communicationMenu->addChild(
//                'menu.government_control',
//                array(
//                    'uri' => 'http://people.culture.ru/forum_private/'
//                )
//            );
//
//            $communicationMenu->addChild(
//                'menu.open_government',
//                array(
//                    'uri' => 'http://people.culture.ru/opengov/expert/'
//                )
//            );
//
            /*
            $communicationMenu->addChild(
                'menu.government_services',
                array(
                    'route' => 'armd_main_services'
                )
            );

            $communicationMenu->addChild(
                'menu.social_council',
                array(
                    'uri' => 'http://people.culture.ru/community_council/'
                )
            );

            $communicationMenu->addChild(
                'menu.culture_sites',
                array(
                    'route' => 'armd_external_search_results'
                )
            );
            */

            //--- /Communication

            /*
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
            
            $specialMenu->addChild(
                'menu.intermuseum',
                array(
                    'route' => 'armd_main_intermuseum'
                )
            );
            //--- /Special
            */


        }



        $menu->setCurrentUri($request->getRequestUri());

        return $menu;
    }


    public function addCinemaMenuItems(ItemInterface $item) {
        $genres = $this->em->getRepository('ArmdLectureBundle:LectureGenre')
            ->createQueryBuilder('g')
            ->innerJoin('g.lectureSuperType', 'st')
            ->where('st.code = :super_type_code')
            ->andWhere('g.level = :level')
            ->orderBy('g.sortIndex',  'ASC')
            ->addOrderBy('g.id',  'ASC')
            ->setParameters(
                array(
                    'super_type_code' => 'LECTURE_SUPER_TYPE_CINEMA',
                    'level' => 1
                )
            )
            ->getQuery()->getResult();

        foreach ($genres as $genre) {
            $item->addChild(
                $genre->getTitle(),
                array(
                    'route' => 'armd_lecture_cinema_index',
                    'routeParameters' => array('genreSlug' => $genre->getSlug())
                )
            );
        }

    }

}