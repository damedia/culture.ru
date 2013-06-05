<?php

namespace Armd\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

class ViewedContentController extends Controller
{
    /**
     * Items per page
     * @var integer
     */
    protected $itemsPerPage = 20;

    /**
     * List action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($page = 1)
    {
        if (1 == mt_rand(1, 50)) {
            $this->clearOldRecords();
        }

        $user       = $this->getAuthUser();
        $pagination = $this->getPagination($page, $user);

        return $this->render(
            'ArmdUserBundle:ViewedContent:list.html.twig',
            array('pagination' => $pagination)
        );
    }

    /**
     * Clear action
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function clearAction()
    {
        $user = $this->getAuthUser();
        $list = $this->getEntityRepository()->findBy(array('user' => $user));

        if ($list) {
            $em = $this->getEntityManager();

            foreach ($list as $item) {
                $em->remove($item);
            }

            $em->flush();
        }

        return new RedirectResponse($this->get('router')->generate('armd_user_viewed_content'));
    }

    /**
     * Settings action
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateUserAction($storeViewedContent)
    {
        $user        = $this->getAuthUser();
        $userManager = $this->get('fos_user.user_manager');

        $user->setStoreViewedContent($storeViewedContent == 'on');
        $userManager->updateUser($user);

        if ($storeViewedContent == 'off') {
            return $this->clearAction();
        }

        return new RedirectResponse($this->get('router')->generate('armd_user_viewed_content'));
    }

    /**
     * Get entity manager
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->get('doctrine')->getEntityManager();
    }

    /**
     * Get entity repository
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getEntityRepository()
    {
        return $this->get('doctrine')->getRepository('ArmdUserBundle:ViewedContent');
    }

    /**
     * Get auth user
     * @return \Armd\UserBundle\Entity\User
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function getAuthUser()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $user;
    }

    /**
     * Get pagination
     * @param integer $page
     * @param Armd\UserBundle\Entity\User $user
     * @return Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    protected function getPagination($page, $user)
    {
        $qb = $this->getEntityRepository()->createQueryBuilder('vc');
        $qb
            ->where('vc.user = :user')
                ->setParameter('user', $user)
            ->orderBy('vc.date', 'DESC');
        
        $countQb = $this->getEntityRepository()->createQueryBuilder('vc');
        $countQb
            ->select('COUNT(vc.id) as total')
            ->where('vc.user = :user')
                ->setParameter('user', $user);

        $count = $countQb->getQuery()->getSingleResult();
        
        if ($count['total'] != 1) {
            $queryObject = $qb;

        } else {
            $queryObject = $qb->getQuery()->getResult();
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($queryObject, $page, $this->itemsPerPage, array('distinct' => false));

        return $pagination;
    }

    /**
     * Clear old records
     */
    protected function clearOldRecords()
    {
        $clearCommand = $this->get('armd_user.command.viewed_content_clear');
        $clearCommand->execute(new ArgvInput(array()), new NullOutput());
    }
}
