<?php
namespace Armd\UserBundle\Admin\Entity;

use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Armd\MkCommentBundle\Entity\Notice;


class UserAdmin extends BaseAdmin
{

    protected $translationDomain = 'ArmdUserBundle';

    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);

        $this->setTemplate('list', 'ArmdUserBundle:Admin:list.html.twig');

        $listMapper->add('actions', 'text', array('template' => 'ArmdUserBundle:Admin:user_row_actions.html.twig'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);
        $formMapper
            ->with('Profile')
            ->add(
                'avatar',
                'armd_media_file_type',
                array(
                    'required' => false,
                    'with_remove' => true,
                    'media_context' => 'user_avatar',
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'thumbnail'
                )
            )
            ->add('region')
            ->add('biographyText')
            ->end()
            ->with('Social')
            ->add('vkontakteUid')
            ->end()
            ->with('Mailing Lists')
            ->add('subscriptions', null, array('expanded' => true))
            ->end()
            ->with('Comments')
            ->add(
                'noticeOnComment',
                'choice',
                array(
                    'required' => false,
                    'choices' => array(
                        Notice::T_NONE => $this->trans('NOTICE_T_NONE', array(), 'ArmdMkCommentBundle'),
                        Notice::T_REPLY => $this->trans('NOTICE_T_REPLY', array(), 'ArmdMkCommentBundle'),
                        Notice::T_THREAD => $this->trans('NOTICE_T_THREAD', array(), 'ArmdMkCommentBundle'),
                        Notice::T_ALL => $this->trans('NOTICE_T_ALL', array(), 'ArmdMkCommentBundle'),
                    )
                )
            )
            ->end();

        $formMapper
            ->remove('facebookUid')
            ->remove('twitterUid')
            ->remove('gplusUid')
            ->remove('gplusName')
            ->remove('biography');
    }

}

