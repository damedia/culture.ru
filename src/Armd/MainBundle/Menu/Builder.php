<?php
namespace Armd\MainBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        //$t = $this->container->get('translator');

        $menu = $factory->createItem('root');

        $mainMenu = $menu->addChild(
                'menu.homepage',
            array(
                'route' => 'armd_main_homepage'
            )
        );

        $mainMenu->addChild(
            'menu.atlas',
            array(
                'route' => 'armd_atlas_index'
            )
        );

        $mainMenu->addChild(
            'menu.russia_images',
            array(
                'route' => 'armd_atlas_index'
            )
        );

        $mainMenu->addChild(
            'menu.events',
            array(
                'uri' => '#'
            )
        );

        $mainMenu->addChild(
            'menu.cinema',
            array(
                'route' => 'armd_lecture_lecture_index'
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


        $menu->addChild(
            'menu.events',
            array(
                'route' => 'armd_news_list_index',
            )
        );

        $menu->addChild(
            'menu.information',
            array(
                'uri' => '#'
            )
        );

        $menu->addChild(
            'menu.communication',
            array(
                'uri' => '#'
            )
        );

        $menu->addChild(
            'menu.special_projects',
            array(
                'uri' => '#'
            )
        );

        $menu->setCurrentUri($this->container->get('request')->getRequestUri());

        return $menu;
    }
}