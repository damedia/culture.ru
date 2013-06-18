<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\MainBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\Admin;
use Armd\MainBundle\EventListener\AdminShowOnMainFormSubscriber;
use Sonata\AdminBundle\Route\RouteCollection;

class ShowOnMain extends Admin
{
    protected $container;

    public function __construct($code, $class, $baseControllerName, $serviceContainer)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $serviceContainer;
    }
    
    public static function getFields()
    {
        return array(
            'virtualTours' => 'Armd\MuseumBundle\Entity\Museum',
            //'lectures' => 'Armd\LectureBundle\Entity\Lecture',
            //'news' => 'Armd\NewsBundle\Entity\News',
            'objects' => 'Armd\AtlasBundle\Entity\Object',
        );
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $builder = $formMapper->getFormBuilder();
        $builder->addEventSubscriber(new AdminShowOnMainFormSubscriber($builder->getFormFactory(), $this->container));        
        $newsCategories = $this->container->get('armd_news.manager.news')->getCategories();
        $lectureSuperTypes = $this->container->get('doctrine')
            ->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findBy(array(), array('name' => 'ASC'));
        
        $formMapper->with('General');
        
        foreach (self::getFields() as $name => $class) {
            $formMapper->add($name, 'text', array(
                'required' => false,
                'virtual' => true,
                'attr' => array(
                    'class' => 'select2-show-on-main span5', 
                    'data-field' => $name                      
                ),
            ));
        } 
        
        $formMapper->end();
        $formMapper->with('Lectures');
        
        foreach ($lectureSuperTypes as $category) {
            $name = 'lectures-' . $category->getId();
            $formMapper->add($name, 'text', array(
                'required' => false,
                'label' => $category->getName(),
                'virtual' => true,
                'attr' => array(
                    'class' => 'select2-show-on-main span5', 
                    'data-field' => $name                      
                ),
            ));
        }
        
        $formMapper->end();
        $formMapper->with('Media');
        
        foreach ($newsCategories as $category) {
            $name = 'news-' . $category->getId();
            $formMapper->add($name, 'text', array(
                'required' => false,
                'label' => $category->getTitle(),
                'virtual' => true,
                'attr' => array(
                    'class' => 'select2-show-on-main span5', 
                    'data-field' => $name                      
                ),
            ));
        }
        
        $formMapper->end();        
                    
        parent::configureFormFields($formMapper);
    }   
    
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('list');
        $collection->remove('edit');
        $collection->add('list', 'create');
        $collection->add('edit', 'create');
    }  
    
    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'ArmdMainBundle:CRUD:show_on_main_edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }
}
