<?php

namespace Armd\LectureBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityRepository;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class LectureTranslationAdmin extends Admin
{
    protected $translationDomain = 'ArmdLectureBundle';
    protected $classnameLabel = 'Translation';
    protected $baseRouteName = 'lecture_translation';
    protected $baseRoutePattern = 'lecture_translation';


    public function createQuery($context = 'list')
    {

        $qb = $this->modelManager->getEntityManager('ArmdLectureBundle:Lecture')
            ->getRepository('ArmdLectureBundle:Lecture')->createQueryBuilder('l')
            ->innerJoin('l.lectureSuperType', 'st')
            ->where('st.code = \'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION\'');

        return new ProxyQuery($qb);
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
            ->add('title')
            ->add('description')
            ->add('categories')
            ->add('createdAt')
            ->add('lecturer')
            ->add('recommended')
            ->add('lectureVideo')
            ->add('lectureFile');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $lecture = $this->getSubject();
        $superType = $this->modelManager->getEntityManager('ArmdLectureBundle:LectureSuperType')
            ->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode('LECTURE_SUPER_TYPE_VIDEO_TRANSLATION');

        if (!$lecture->getId()) {
            $lecture->setLectureSuperType($superType);

        } elseif ($lecture->getLectureSuperType()->getId() !== $superType->getId()) {
            throw new \RuntimeException('You can not edit video with type "' .$lecture->getLectureSuperType()->getName() .'" as video with type "' .$superType->getName() .'"');
        }

        $formMapper
            ->add('published')
            ->add('title')
            ->add('description')
            ->add('categories', 'armd_lecture_categories',
            array(
                'required' => false,
                'attr' => array('class' => 'chzn-select atlas-object-categories-select'),
                'super_type' => $superType
            ))
            ->add('tags', 'armd_tag', array('required' => false, 'attr' => array('class' => 'select2-tags')))
            ->add('recommended')
            /*->with('Tvigle video')
                ->add('lectureVideo', 'armd_tvigle_video_selector', array('required' => false))
            ->end()*/
            ->with('Video')
                ->add('mediaLectureVideo', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'lecture')))
            ->end()
            ->with('External Video')
                ->add('externalUrl', null, array('required' => false))
            ->end()
        ;
    }


    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('published')
            ->add('title')
            ->add('categories');
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
            ->add('createdAt')
            ->add('categories', null, array('template' => 'ArmdLectureBundle:Admin:list_lecture_categories.html.twig'))
        ;
    }

    public function getFormTheme()
    {
        $themes = parent::getFormTheme();
        $themes[] = 'ArmdLectureBundle:Form:fields.html.twig';
        $themes[] = 'ArmdMediaHelperBundle:Form:fields.html.twig';
        return $themes;
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

    public function setContainer($container)
    {
        $this->container = $container;
    }

    protected function saveTagging($object)
    {
        $this->container->get('fpn_tag.tag_manager')->saveTagging($object);
    }
}