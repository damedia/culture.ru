<?php
namespace Armd\MainBundle\Menu;

use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware {
    private $factory;
    private $em;

    /**
     * @param FactoryInterface $factory
     * @param EntityManager $em
     */
    public function __construct(FactoryInterface $factory, EntityManager $em) {
        $this->factory = $factory;
        $this->em = $em;
    }



    /*==================
    ==== OLD STUFF ===*/
    public function createMainMenu(Request $request) {
        $menu = $this->factory->createItem('root');

        //News
        $newsMenu = $menu->addChild('menu.news', array('route' => 'armd_news_index_by_category',
                                                       'routeParameters' => array('category' => 'news')));

        //Media
        $mediaMenu = $menu->addChild('menu.media', array('route' => 'armd_news_index_by_category',
                                                         'routeParameters' => array('category' => 'reportages')));
        $mediaMenu->addChild('menu.reportage', array('route' => 'armd_news_index_by_category',
                                                     'routeParameters' => array('category' => 'reportages')));
        $mediaMenu->addChild('menu.interview', array('route' => 'armd_news_index_by_category',
                                                     'routeParameters' => array('category' => 'interviews')));
        $mediaMenu->addChild('menu.article', array('route' => 'armd_news_index_by_category',
                                                   'routeParameters' => array('category' => 'articles')));
        $mediaMenu->addChild('menu.events', array('route' => 'armd_news_index_by_category',
                                                  'routeParameters' => array('category' => 'events')));
        $mediaMenu->addChild('menu.lectures_news', array('route' => 'armd_lecture_news_index'));

        //ImagesOfRussia
        $imagesOfRussiaMenu = $menu->addChild('menu.russia_images', array('route' => 'armd_atlas_russia_images'));

        //Museums
        $museumsMenu = $menu->addChild('menu.museum', array('route' => 'armd_museum_virtual'));
        $museumsMenu->addChild('menu.virtual_museum', array('route' => 'armd_museum_virtual'));
//      $museumsMenu->addChild('menu.museum_guide', array('route' => 'armd_museum_guide_index'));
        $museumsMenu->addChild('menu.museum_lesson', array('route' => 'armd_lesson_list'));
        $museumsMenu->addChild('menu.museum_reserve', array('route' => 'armd_main_museum_reserve'));
        $museumsMenu->addChild('menu.galley_of_war', array('route' => 'armd_war_gallery'));

        //Movies
        $moviesMenu = $menu->addChild('menu.cinema', array('route' => 'armd_lecture_cinema_index',
                                                           'routeParameters' => array('genreSlug' => 'feature-film')));
        $this->addCinemaMenuItems($moviesMenu);

        //Theaters
        $theatersMenu = $menu->addChild('menu.theaters', array('route' => 'armd_theater_list'));
        $theatersMenu->addChild('menu.perfomance', array('route' => 'armd_perfomance_list'));

        //Music
        $musicMenu = $menu->addChild('menu.music', array('route' => 'armd_main_underconstruction'));

        //Lectures
        $lecturesMenu = $menu->addChild('menu.lectures', array('route' => 'armd_lecture_lecture_index'));

        //Atlas
        $atlasMenu = $menu->addChild('menu.atlas', array('route' => 'armd_atlas_index'));

        //Kids
//      $kidsMenu = $menu->addChild('menu.kids', array('uri' => '/kids/children.html'));

        //Communication
//      $communicationMenu = $menu->addChild('menu.communication', array('uri' => 'http://people.culture.ru/forum/'));
//      $communicationMenu->addChild('menu.forum', array('uri' => 'http://people.culture.ru/forum/'));
//      $communicationMenu->addChild('menu.government_control', array('uri' => 'http://people.culture.ru/forum_private/'));
//      $communicationMenu->addChild('menu.open_government', array('uri' => 'http://people.culture.ru/opengov/expert/'));
//      $communicationMenu->addChild('menu.government_services', array('route' => 'armd_main_services'));
//      $communicationMenu->addChild('menu.social_council', array('uri' => 'http://people.culture.ru/community_council/'));
//      $communicationMenu->addChild('menu.culture_sites', array('route' => 'armd_external_search_results'));

        //SpecialProjects
        $specialProjectsMenu = $menu->addChild('menu.special_projects', array('route' => 'armd_main_project_1150'));
        $specialProjectsMenu->addChild('menu.special_projects_1150', array('route' => 'armd_main_project_1150'));
        $specialProjectsMenu->addChild('menu.special_projects_romanov450', array('route' => 'armd_news_item_by_category',
                                                                                 'routeParameters' => array('category' => 'news', 'id' => 955)));
        $specialProjectsMenu->addChild('menu.borodino', array('route' => 'armd_main_project_brodino'));
        $specialProjectsMenu->addChild('menu.special_projects_stanislavski', array('route' => 'armd_news_item_by_category',
                                                                                   'routeParameters' => array('category' => 'news', 'id' => 3724)));
        $specialProjectsMenu->addChild('menu.intermuseum', array('route' => 'armd_main_intermuseum'));

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
            ->setParameters(array('super_type_code' => 'LECTURE_SUPER_TYPE_CINEMA', 'level' => 1))
            ->getQuery()->getResult();

        foreach ($genres as $genre) {
            $item->addChild($genre->getTitle(), array('route' => 'armd_lecture_cinema_index',
                'routeParameters' => array('genreSlug' => $genre->getSlug())));
        }
    }



    /*==================
    ==== NEW STUFF ===*/
    public function createNewMainMenu(Request $request) {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array('class' => 'b-menu'));

        //Media
        $mediaMenu = $menu->addChild('menu.media', array('route' => 'armd_news_index_by_category'));
        $mediaMenu->setAttribute('class', 'm-color-1');

        //Blog
        //$blogMenu = $menu->addChild('menu.blog', array('route' => 'blog_list'));
        //$blogMenu->setAttribute('class', 'm-color-2');

        //ImagesOfRussia
        $imagesOfRussiaMenu = $menu->addChild('menu.russia_images', array('route' => 'armd_atlas_russia_images'));
        $imagesOfRussiaMenu->setAttribute('class', 'm-color-2');

        //Museums
        $museumsMenu = $menu->addChild('menu.museum', array('route' => 'armd_museum_virtual'));
        $museumsMenu->setAttribute('class', 'm-color-3');

        //Movies
        $moviesMenu = $menu->addChild('menu.cinema', array('route' => 'armd_lecture_cinema_index'));
        $moviesMenu->setAttribute('class', 'm-color-4');

        //Theaters
        $theatersMenu = $menu->addChild('menu.theater', array('route' => 'armd_theaters_hub')); //armd_theater_list
        $theatersMenu->setAttribute('class', 'm-color-5');

        //Performances
        $performancesMenu = $menu->addChild('menu.perfomance', array('route' => 'armd_perfomance_list')); //TODO: typo in the route name
        $performancesMenu->setAttribute('class', 'm-color-6');

        //Music
        $musicMenu = $menu->addChild('menu.music', array('route' => 'armd_main_underconstruction'));
        $musicMenu->setAttribute('class', 'm-color-7');

        //Lectures
        $lecturesMenu = $menu->addChild('menu.lectures', array('route' => 'armd_lecture_lecture_index'));
        $lecturesMenu->setAttribute('class', 'm-color-8');

        //Atlas
        $atlasMenu = $menu->addChild('menu.atlas', array('route' => 'armd_atlas_index'));
        $atlasMenu->setAttribute('class', 'm-color-9');

        //SpecialProjects
        $specialProjectsMenu = $menu->addChild('menu.special_projects', array('route' => 'damedia_special_project_list'));
        $specialProjectsMenu->setAttribute('class', 'm-color-10');

        $menu->setCurrentUri($request->getRequestUri());

        return $menu;
    }

    public function createFooterMenu(Request $request) { //TODO: code duplicates top menu completely (except CSS classes)!
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array('class' => 'footer-menu'));

        //Media
        $menu->addChild('menu.news', array('route' => 'armd_news_index_by_category'));

        //Blog
        //$blogMenu = $menu->addChild('menu.blog', array('route' => 'blog_list'));

        //ImagesOfRussia
        $menu->addChild('menu.russia_images', array('route' => 'armd_atlas_russia_images'));

        //Museums
        $menu->addChild('menu.museum', array('route' => 'armd_museum_virtual'));

        //Movies
        $menu->addChild('menu.cinema', array('route' => 'armd_lecture_cinema_index',
                                             'routeParameters' => array('genreSlug' => 'feature-film')));

        //Theaters
        $menu->addChild('menu.theaters', array('route' => 'armd_theater_list'));

        //Performances
        $menu->addChild('menu.perfomance', array('route' => 'armd_perfomance_list')); //TODO: typo in the route name

        //Music
        $menu->addChild('menu.music', array('route' => 'armd_main_underconstruction'));

        //Lectures
        $menu->addChild('menu.lectures', array('route' => 'armd_lecture_lecture_index'));

        //Atlas
        $menu->addChild('menu.atlas', array('route' => 'armd_atlas_index'));

        //SpecialProjects
        $menu->addChild('menu.special_projects', array('route' => 'damedia_special_project_list'));

        $menu->setCurrentUri($request->getRequestUri());

        return $menu;
    }
}