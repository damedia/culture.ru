<?php

namespace Armd\BlogBundle\Controller;

use Armd\UserBundle\Repository\ViewedContentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        $menu = $this->get('armd_main.menu.main');
        $menuFinder = $this->get('armd_main.menu_finder');
        if (!$menuFinder->findByUri($menu, $this->getRequest()->getRequestUri())) {
            $menu->setCurrentUri(
                $this->get('router')->generate('blog_list')
            );
        }

        $user = null == $request->get('user') ? null : $request->get('user');

        if (null !== $user) {
            $user = $this->getDoctrine()->getManager()->getRepository('ArmdUserBundle:User')->find($user);
        }

        $blogs = $this->getDoctrine()->getManager()->getRepository('BlogBundle:Blog')->getPostsByUser($user);

        $paginator = $this->get('knp_paginator');
        $page = $this->get('request')->get('page', 1);

        $pagination = $paginator->paginate(
            $blogs,
            $page,
            2
        );

        $paginationData = $pagination->getPaginationData();
        $blogIds = array();
        foreach ($pagination as $row) {
            $blogIds[] = $row->getId();
        }

        /** @var ViewedContentRepository $repo */
        $repo = $this->getDoctrine()->getRepository('ArmdUserBundle:ViewedContent');
        $stats = $repo->getBlogStatsByIds($blogIds);

        if ($request->isXmlHttpRequest()) {
            $response = array(
                'isSuccessful' => true,
                'html' => $this->renderView(
                    'BlogBundle:Default:list.ajax.html.twig',
                    array('blogs' => $pagination, 'stats' => $stats, 'expandFirst' => false)
                )
            );

            if ($page == $paginationData['last']) {
                $response['finish'] = true;
            }

            return new JsonResponse($response);
        } else {
            return $this->render(
                'BlogBundle:Default:index.html.twig',
                array(
                    'blogs' => $pagination,
                    'stats' => $stats,
                    'paginationData' => $paginationData,
                    'user' => $user
                )
            );
        }

    }

    public function viewAction(Request $request)
    {
        // menu fix
        $menu = $this->get('armd_main.menu.main');
        $menuFinder = $this->get('armd_main.menu_finder');
        if (!$menuFinder->findByUri($menu, $this->getRequest()->getRequestUri())) {
            $menu->setCurrentUri(
                $this->get('router')->generate('armd_news_list_index')
            );
        }

        $entity = $this->getDoctrine()->getManager()->getRepository('BlogBundle:Blog')->find($request->get('id'));

        if (null === $entity) {
            throw $this->createNotFoundException(sprintf('Unable to find record %d', $request->get('id')));
        }

        return $this->render(
            'BlogBundle:Default:item.html.twig',
            array(
                'entity' => $entity,
                'comments' => $this->getComments($entity->getThread()),
                'thread' => $entity->getThread(),
            )
        );
    }


    public function bloggersAction($user)
    {
        $userManager = $this->container->get('fos_user.user_manager.default');
        $bloggers = $userManager->getBloggers();

        return $this->render(
            'BlogBundle:Default:bloggers.html.twig',
            array(
                'bloggers' => $bloggers,
                'user' => $user
            )
        );
    }


    public function popularAction(Request $request)
    {
        /** @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->getDoctrine()->getManager()->getRepository('ArmdUserBundle:ViewedContent');
        $entity = null;
        $qb = $repository->createQueryBuilder('vc')
            ->select('COUNT(vc.id) as viewCount, vc.entityId')
            ->where('vc.entityClass = :ec')
            ->groupBy('vc.entityId')
            ->setParameter('ec', 'ArmdBlogBundle:Blog')
            ->orderBy('viewCount', 'DESC')
            ->getQuery()
            ->getScalarResult();

        $paginator = $this->get('knp_paginator');
        $page = $this->get('request')->get('page', 1);

        $pagination = $paginator->paginate(
            $qb,
            $page,
            1
        );

        // wat
        foreach ($pagination as $item) {
            $entity = $this->getDoctrine()->getManager()->getRepository('BlogBundle:Blog')->find($item['entityId']);
        }

        $paginationData = $pagination->getPaginationData();

        if ($request->isXmlHttpRequest()) {

            if ($entity) {
                $response = array(
                    'isSuccessful' => true,
                    'html' => $this->renderView(
                        'BlogBundle:Default:popular.html.twig',
                        array(
                            'page' => $page,
                            'entity' => $entity,
                            'paginationData' => $paginationData
                        )
                    )
                );
            } else {
                $response = array(
                    'isSuccessful' => false,
                    'html' => ''
                );
            }

            if ($page == $paginationData['last']) {
                $response['finish'] = true;
            }

            return new JsonResponse($response);
        } else {
            if ($entity) {
                return $this->render(
                    'BlogBundle:Default:popular.html.twig',
                    array(
                        'page' => $page,
                        'entity' => $entity,
                        'paginationData' => $paginationData
                    )
                );
            } else {
                return new Response('');
            }
        }
    }


    public function lastPostsAction($user)
    {
        $items = $this->getDoctrine()->getManager()->getRepository('BlogBundle:Blog')->getLastPostsByUser($user);

        return $this->render(
            'BlogBundle:Default:last_posts.html.twig',
            array(
                'user' => $user,
                'items' => $items
            )
        );
    }


    public function lastPostAction()
    {
        $entity = $this->getDoctrine()->getManager()->getRepository('BlogBundle:Blog')->getLast();

        return $this->render(
            'BlogBundle:Default:last.html.twig',
            array(
                'entity' => $entity
            )
        );
    }

    /**
     * @param Thread $thread
     * @return null
     */
    protected function getComments(Thread $thread = null)
    {
        if (empty($thread)) {
            return null;
        } else {
            return $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);
        }
    }

    /**
     * @return \Armd\TagBundle\Entity\TagManager
     */
    public function getTagManager()
    {
        return $this->get('fpn_tag.tag_manager');
    }

}
