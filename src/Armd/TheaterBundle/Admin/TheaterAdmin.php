<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\TheaterBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

class TheaterAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_by'      => 'title',    
        '_sort_order'   => 'ASC',
    );

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('published')
            ->add('title')
            ->add('description')
            ->add('director')
            ->add('address')
            ->add('url')
            ->add('email')
            ->add('phone')
            ->add('metro')
            ->add('ticketOfficeMode')
            ->add('image')
        ;
        
        parent::configureShowFields($showMapper);
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('published', null, array('required' => false))
                ->add('title')
                ->add('city', null, array(
                    'required' => true,
                    'property' => 'title',
                    'label' => 'City',
                    'attr' => array('class' => 'chzn-select span5'),
                    'query_builder' => function($er) {
                        $qb = $er->createQueryBuilder('r');
                        $qb->orderBy('r.title', 'ASC');
                        return $qb;
                    }
                ))
                ->add('categories', null, array('attr' => array('class' => 'chzn-select span5')))
                ->add('description', null, array('attr' => array('class' => 'tinymce')))
                ->add('director')
                ->add('address')               
                ->add('url')
                ->add('email')
                ->add('phone')  
                ->add('metro')
                ->add('ticketOfficeMode')
            ->end()   
            ->with('Map')
                ->add('latitude', 'text', array('required' => false, 'attr' => array('class' => 'geopicker lat')))
                ->add('longitude', 'text', array('required' => false, 'attr' => array('class' => 'geopicker lon')))
            ->end()
            ->with('Media')                
                ->add('image', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'theater')))               
                ->add('gallery', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'theater')))
            ->end()
            ->with('Tvigle Video')                      
                /*->add('interviews', 'collection', array(
                    'required' => false,
                    'type' => 'armd_tvigle_video_selector',
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'attr' => array('class' => 'armd-sonata-tvigle-collection'),
                    'options' => array('attr' => array('class' => 'armd-sonata-tvigle-form')),
                    'label' => 'Interviews'
                ))*/
                ->add('mediaInterviews', 'collection', array(
                    'type' => 'armd_media_video_type',
                    'options' => array(
                        'media_context' => 'theater',
                        'media_provider' => 'sonata.media.provider.tvigle',
                        'media_format' => 'thumbnail',
                        'with_title' => true
                    ),
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => false,
                    'attr' => array('class' => 'armd-sonata-images-collection'),
                    'label' => 'Видео (Tvigle ID)'
                ))
            ->end()            
            ;

        parent::configureFormFields($formMapper);
    }
    
    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'ArmdTheaterBundle:Form:edit_container.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
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
            ->add('published')                       
        ;
        
        parent::configureListFields($listMapper);        
    }
    
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('published')
            ->add('title')
            ->add('city')
        ;
    }        
}
