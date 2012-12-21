<?php
namespace Armd\MainBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $t = $this->container->get('translator');

        $menu = $factory->createItem('root');

        $aboutMenu = $menu->addChild(
            'menu.about',
            array(
                'route' => 'armd_main_homepage'
            ),
            array('label' => $t->trans('menu.about'))
        );

        $aboutMenu->addChild(
            'menu.about_history',
            array(
                'route' => 'armd_main_page',
                'routeParameters' => array('page' => 'about_history')
            )
        );

        $aboutMenu->addChild(
            'menu.about_structure',
            array(
                'route' => 'armd_main_page',
                'routeParameters' => array('page' => 'about_structure')
            )
        );
        $aboutMenu->addChild(
            'menu.about_lottery',
            array(
                'route' => 'armd_main_page',
                'routeParameters' => array('page' => 'about_lottery')
            )
        );
//        $aboutMenu->addChild(
//            'menu.about_programs',
//            array(
//                'uri' => '#',
////                'route' => 'armd_main_page',
////                'routeParameters' => array('page' => 'about_programs')
//            )
//        );
//        $aboutMenu->addChild(
//            'menu.about_partners',
//            array(
//                'uri' => '#',
////                'route' => 'armd_main_page',
////                'routeParameters' => array('page' => 'about_partners')
//            )
//        );
        $aboutMenu->addChild(
            'menu.about_contacts',
            array(
                'uri' => '#',
                'route' => 'armd_main_page',
                'routeParameters' => array('page' => 'about_contacts')
            )
        );

        $eventsMenu = $menu->addChild(
            'menu.events',
            array(
                'uri' => '#',
//                'route' => 'armd_main_page',
//                'routeParameters' => array('page' => 'events')
            )
        );

//        $eventsMenu->addChild('events.news', array('route' => 'armd_main_page', 'routeParameters' => array('page' => 'events_news')));

        $depoMenu = $menu->addChild(
            'menu.depo',
            array(
                'uri' => '#',
//                'route' => 'armd_main_page',
//                'routeParameters' => array('page' => 'depo')
            )
        );

        $menu->setCurrentUri($this->container->get('request')->getRequestUri());

        return $menu;
    }
}