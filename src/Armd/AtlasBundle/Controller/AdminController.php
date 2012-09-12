<?php

namespace Armd\AtlasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdminController extends Controller
{
    /**
     * Some temp or test actions here
     *
     * @Route("/test")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function testAction()
    {
        $testType = $this->getRequest()->get('type');

        switch($testType) {
            case 'list_without_3d':
                $objects = $this->getDoctrine()->getManager()->getRepository('ArmdAtlasBundle:Object')
                    ->createQueryBuilder('o')
                    ->where('o.image3d IS NULL')
                    ->getQuery()
                    ->getResult();

                $response = '';
                foreach($objects as $object) {
                    $response .= $object->getId() . ' ' . $object->getTitle() . '<br>';
                }
                return new Response($response);
                break;

            case 'list_with_virtual_tour_image':
                $objects = $this->getDoctrine()->getManager()->getRepository('ArmdAtlasBundle:Object')
                    ->createQueryBuilder('o')
                    ->where('o.virtualTourImage IS NOT NULL')
                    ->getQuery()
                    ->getResult();

                $response = '';
                foreach($objects as $object) {
                    $response .= $object->getId() . ' ' . $object->getTitle() . '<br>';
                }
                return new Response($response);
                break;
        }

        return new Response('ok');
    }


    /**
     * @Route("/category_up/{id}", name="armd_atlas_admin_category_tree_up")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function upAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Category');
        $category = $repo->findOneById($id);
        if ($category->getParent()) {
            $repo->moveUp($category);
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * @Route("/category_down/{id}", name="armd_atlas_admin_category_tree_down")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function downAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Category');
        $category = $repo->findOneById($id);
        if ($category->getParent()) {
            $repo->moveDown($category);
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * @Route("/delete_category/{id}", name="armd_atlas_admin_category_delete")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function deleteCategoryAction()
    {
        return new Response('not implemented');
    }

    /**
     * @Route("/export_objects")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function exportObjects()
    {
        $objects = $this->getDoctrine()->getManager()->getRepository('ArmdAtlasBundle:Object')->findAll();
        $categories = $this->getDoctrine()->getManager()->getRepository('ArmdAtlasBundle:Category')->childrenHierarchy();

        $content = $this->renderView('ArmdAtlasBundle:Admin:export_objects.xml.twig', array(
            'objects' => $objects,
            'categories' => $categories
        ));

        $response = new Response($content, 200, array('content-type' => 'text/xml'));
//        $response = new Response($content, 200);
        return $response;
    }

    /**
     * @Route("/rebuild_tree")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function rebuildTreeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repairer = new \Armd\AtlasBundle\Util\TreeRepairer();
        $repairer->rebuildTree($em, $em->getRepository('ArmdAtlasBundle:Category'));
        return new Response('ok');
    }

    /**
     * @Route("/verify_tree")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function verifyTreeAction()
    {
        $errors = $this->getDoctrine()->getManager()
            ->getRepository('ArmdAtlasBundle:Category')->verify();
        var_dump($errors);
        return new Response();
    }

    /**
     * @Route("/list-user-objects/user/{userId}",
     * name="armd_atlas_admin_list_user_objects",
     * options={"expose"=true})
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     * @Template()
     */
    public function listUserObjectsAction($userId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ArmdUserBundle:User')->find($userId);
        if(empty($user)) {
            throw new \InvalidArgumentException('User not found');
        }
        $userObjects = $this->get('armd_atlas.manager.object')->getUserObjects($user);

        $qbOtherObjects = $em->getRepository('ArmdAtlasBundle:Object')
            ->createQueryBuilder('o')
            ->orderBy('o.title', 'ASC');
        if(count($userObjects)) {
            $qbOtherObjects->where('o NOT IN (:objects)')
            ->setParameter('objects', $userObjects);
        }
        $otherObjects = $qbOtherObjects->getQuery()->getResult();

        return array(
            'user' => $user,
            'userObjects' => $userObjects,
            'otherObjects' => $otherObjects,
        );

    }

    /**
     * @Route("/grant-user-object/user/{userId}/object/{objectId}",
     *  name="armd_atlas_admin_grant_user_object",
     *  options={"expose"=true}
     * )
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function grantUserObjectAction($userId, $objectId)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('ArmdUserBundle:User')->find($userId);
        if(empty($user)) {
            throw new \InvalidArgumentException('User not found');
        }

        $object = $em->getRepository('ArmdAtlasBundle:Object')->find($objectId);
        if(empty($object)) {
            throw new \InvalidArgumentException('Atlas object not found');
        }

        $aclManager = $this->get('armd_user.manager.acl');
        $aclManager->grant($user, $object, MaskBuilder::MASK_EDIT | MaskBuilder::MASK_VIEW);

//        return new Response(json_encode(array('resultCode' => 'OK')));
        return $this->forward('ArmdAtlasBundle:Admin:listUserObjects', array('userId' => $userId));
    }

    /**
     * @Route("/revoke-user-object/user/{userId}/object/{objectId}",
     *  name="armd_atlas_admin_revoke_user_object",
     *  options={"expose"=true}
     * )
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function revokeUserObjectAction($userId, $objectId)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('ArmdUserBundle:User')->find($userId);
        if(empty($user)) {
            throw new \InvalidArgumentException('User not found');
        }

        $object = $em->getRepository('ArmdAtlasBundle:Object')->find($objectId);
        if(empty($object)) {
            throw new \InvalidArgumentException('Atlas object not found');
        }

        $aclManager = $this->get('armd_user.manager.acl');
        $aclManager->revoke($user, $object, MaskBuilder::MASK_EDIT | MaskBuilder::MASK_VIEW);

        return $this->forward('ArmdAtlasBundle:Admin:listUserObjects', array('userId' => $userId));

//        return new Response(json_encode(array('resultCode' => 'OK')));
    }

}