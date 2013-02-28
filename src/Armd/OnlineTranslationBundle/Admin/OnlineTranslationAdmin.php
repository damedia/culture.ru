<?php

namespace Armd\OnlineTranslationBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Admin\Admin;

class OnlineTranslationAdmin extends Admin
{
    protected $translationDomain = 'ArmdOnlineTranslationBundle';
    protected $container;

    public function __construct($code, $class, $baseControllerName, $serviceContainer)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $serviceContainer;
    }

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('published')
            ->add('type')
            ->add('title')
            ->add('date')
            ->add('location')
            ->add('shortDescription')
            ->add('fullDescription')
            ->add('dataCode')
            ->add('image');
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
                ->add('type', 'choice', array(
                    'choices'   => array(0 => 'Анонс', 1 => 'Трансляция'),
                    'required'  => true
                ))
                ->add('title')
                ->add('date')
                ->add('location')
                ->add('shortDescription')
                ->add('fullDescription', null, array('attr' => array('class' => 'tinymce')))
                ->add('dataCode')
            ->end()    
            ->with('Media')
                ->add('image', 'armd_media_file_type', array(
                    'required' => false, 
                    'media_context' => 'online_broadcast',
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'default'
                ))
            ->end()    
        ;
        parent::configureFormFields($formMapper);
    }
    
    public function getFormTheme() {
        $themes = parent::getFormTheme();
        $themes[] = 'ArmdOnlineTranslationBundle:Form:fields.html.twig';
        
        return $themes;
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
            ->add('date')
            ->add('type', null, array('template' => 'ArmdOnlineTranslationBundle:Admin:list_online_translation_types.html.twig'))
            ->add('published');
    }
    
    protected function updatePublished($object)
    {
        if ($object->getPublished()) {
            $em = $this->modelManager->getEntityManager('ArmdOnlineTranslationBundle:OnlineTranslation');
            $entities = $em->getRepository('ArmdOnlineTranslationBundle:OnlineTranslation')->findAll();

            foreach ($entities as $entity) {
                if ($entity->getId() != $object->getId()) {
                    $entity->setPublished(false);
                    $em->persist($entity);
                }
            }
            
            $em->flush();
        }
    }

    public function postPersist($object)
    {
        parent::postPersist($object);   
        
        $this->updatePublished($object);
    }

    public function postUpdate($object)
    {
        parent::postUpdate($object);  
        
        $this->updatePublished($object);
    }    
}
