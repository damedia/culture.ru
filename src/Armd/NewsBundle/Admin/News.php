<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\NewsBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

class News extends Admin
{
    protected $translationDomain = 'ArmdNewsBundle';
    protected $container;

    protected $datagridValues = array(
        '_sort_by'      => 'newsDate',    
        '_sort_order'   => 'DESC',
    );

    public function __construct($code, $class, $baseControllerName, $container)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $container;
    }

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('announce')
            ->add('body')
            ->add('newsDate')   
            ->add('date')                            
            ->add('showOnMain')
            ->add('showOnMainOrd')
        ;
        
        parent::configureShowField($showMapper);        
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $article = $this->getSubject();
        if (!empty($article) && $article->getCategory() && !$article->getCategory()->getFiltrable()) {
            $dateFormType = 'armd_simple_date';
        } else {
            $dateFormType = null;
        }

        $formMapper
            ->with('General')
                ->add('newsDate', $dateFormType)
                ->add('title')
                ->add('announce')
                ->add('body', null, array(
                    'attr' => array('class' => 'tinymce'),
                ))
                ->add('source')
            ->end()
            ->with('Главная')
                ->add('showOnMain', null, array(
                    'required' => false
                ))
                ->add('showOnMainOrd', null, array(
                    'required' => false
                ))                
            ->end()
            ->with('Classification')
                ->add('category')
                ->add('tags', 'armd_tag', array('required' => false, 'attr' => array('class' => 'select2-tags')))
                ->add('subject', null, array('required' => false))
                ->add('important', null, array('required' => false))
                ->add('priority')
                ->add('countryDistrict')
            ->end()
            ->with('Publish')
                ->add('published', null, array('required' => false))
                ->add('publishFromDate')
                ->add('publishToDate')
            ->end()
            ->with('Event date')
                ->add('date', $dateFormType, array('required' => false)) // , null, array('date_widget' => 'single_text', 'time_widget' => 'single_text')
                ->add('endDate', $dateFormType, array('required' => false)) // , null, array('date_widget' => 'single_text', 'time_widget' => 'single_text')
            ->end()
            ->with('SEO')
                ->add('seoTitle')
                ->add('seoKeywords')
                ->add('seoDescription')
            ->end()
            ->with('Media')                
                ->add('image', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'news')))
                ->add('gallery', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'news')))
                //->add('video', 'armd_tvigle_video_selector', array('required' => false))
                ->add('mediaVideo', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'news')))
            ->end()
            ->with('Map')
                ->add('isOnMap', null, array('required' => false))
                ->add('lat', 'text', array('required' => false, 'attr' => array('class' => 'geopicker lat')))
                ->add('lon', 'text', array('required' => false, 'attr' => array('class' => 'geopicker lon')))
                ->add('theme')
            ->end()
            ->with('Stuff')
            ->add(
                'stuff',
                'collection',
                array(
                    'type' => 'armd_media_file_type',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'options' => array(
                        'required' => false,
                        'media_provider' => 'sonata.media.provider.file',
                        'by_reference' => true,
                        'media_context' => 'stuff',
                        'media_format' => 'original',
                        'with_remove' => false, // Удаление выше, на уровне коллекции.
                        'with_title' => false,
                        'with_description' => true,
                    ),
                    'attr' => array('class' => 'armd-sonata-images-collection'),
                )
            )
            ->end()
        ;

        parent::configureFormFields($formMapper);
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {        
        $listMapper
            ->addIdentifier('title')
            ->add('showOnMain')
            ->add('showOnMainOrd')
            ->add('newsDate')
            ->add('date')            
            ->add('category')
            ->add('subject')            
            ->add('important')                  
            ->add('published')                            
            ->add('isOnMap')
        ;
        
        parent::configureListFields($listMapper);        
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('category')
            ->add('subject')
            ->add('published')
            ->add('important')
            ->add('isOnMap')
            ->add('showOnMain')
            ->add('showOnMainOrd')
        ;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'ArmdNewsBundle:Form:edit_container.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }


    public function postPersist($object)
    {
        parent::postPersist($object);
        $this->saveTagging($object);
    }

    public function postUpdate($object)
    {
        parent::postUpdate($object);
        $this->saveTagging($object);
    }

    protected function saveTagging($object)
    {
        $this->container->get('fpn_tag.tag_manager')->saveTagging($object);
    }

    public function getBatchActions()
    {
        // retrieve the default (currently only the delete action) actions
        $actions = parent::getBatchActions();

        
        // check user permissions
        if($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute('delete') && $this->isGranted('DELETE')){
            // /*
            $actions['ShowOnMain']=array(
                'label'            => $this->trans('aShowOnMain', array(), 'SonataAdminBundle'),
                'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
            );
            $actions['NotShowOnMain']=array(
                'label'            => $this->trans('aNotShowOnMain', array(), 'SonataAdminBundle'),
                'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
            );
            // */
        }
        
        return $actions;
    }    
}
