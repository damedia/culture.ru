<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\PaperArchiveBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

class PaperArchive extends Admin
{
    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('date')
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
        $formMapper
            ->with('General')
                ->add('edition')
                ->add('title')
                ->add('date')
            ->end()
            ->with('Media')
                ->add('file', 'sonata_type_model_list', array('required' => true), array('link_parameters'=>array('context'=>'paper_archive', 'provider' => 'sonata.media.provider.file')))
                ->add('image', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'paper_archive')))
            ->end();

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
            ->add('edition')
            ->addIdentifier('title')
            ->add('date')
            ->add('image', null, array('template' => 'ArmdPaperArchiveBundle:MediaAdmin:list_custom.html.twig'))
            ->add('file')
        ;

        parent::configureListFields($listMapper);        
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('edition')
            ->add('title')
            ->add('date')
        ;
    }

}
