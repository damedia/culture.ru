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
     * @Route("/test/")
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

            case 'list_with_virtual_tour':
                $objects = $this->getDoctrine()->getManager()->getRepository('ArmdAtlasBundle:Object')
                    ->createQueryBuilder('o')
                    ->where('o.virtualTour IS NOT NULL')
                    ->andWhere('LENGTH(TRIM(o.virtualTour)) > 0 ')
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
     * @Route("/export_objects/{format}", defaults={"format" = "xml"})
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function exportObjects($format)
    {
        $exportDir = realpath(__DIR__ . '/../../../../tmp/atlas_objects');

        $objects = $this->getDoctrine()->getManager()
            ->getRepository('ArmdAtlasBundle:Object')
            ->createQueryBuilder('o')
            ->where('o.published = TRUE')
            ->andWhere('o.showAtRussianImage = TRUE')
            ->getQuery()
            ->getResult();

        $categories = $this->getDoctrine()->getManager()->getRepository('ArmdAtlasBundle:Category')->childrenHierarchy();

        $content = $this->renderView('ArmdAtlasBundle:Admin:export_objects.xml.twig', array(
            'objects' => $objects,
            'categories' => $categories
        ));

        if ($format === 'xml') {
            $response = new Response($content, 200, array('content-type' => 'text/xml'));
        } elseif($format === 'txt') {
            echo "Count: " . count($objects);
            foreach($objects as $object) {
                $filePath = $exportDir . '/atlas_object_' . $object->getId() . '.txt';
                $content = $this->renderView('ArmdAtlasBundle:Admin:export_objects.txt.twig', array('object' => $object));
                $content = html_entity_decode($content);
                $content  = str_replace("\r\n", "\n", $content);
                $content  = str_replace("\r", "\n", $content);
                $content  = str_replace("\n", "\r\n", $content);

                echo $filePath . "<br>";
                file_put_contents($filePath,  $content);
            }

            $response = new Response('Files was written to ' . $exportDir);
        } else {
            $response = new Response('Unknown export format');
        }


        return $response;
    }

    /**
     * @Route("/import_objects_translations")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function importObjectsTranslations()
    {
        $dir = realpath(__DIR__ . '/../../../../tmp/atlas_objects_trans_import');
        if (!is_dir($dir)) {
            return new Response("Directory doesn't exist");
        }
        if (!$dh = opendir($dir)) {
            return new Response("Can't open directory");
        }

        while (($file = readdir($dh)) !== false) {
            if (is_dir($file)) {
                continue;
            }

            if(!preg_match("/\d+/i", $file, $matches)){
                echo "File {$file} must have a digit in name <br>Skipped<br>";
                continue;
            }
            echo "Process file: {$file} <br>";
            $em = $this->getDoctrine()->getEntityManager();
            $object = $em->getRepository('ArmdAtlasBundle:Object')->find($matches[0]);
            echo 'Object: id '.$matches[0].($object ? ' found' : ' not found <br> Skipped<br>').'<br>';
            if(!$object) {
                continue;
            }

            $fileData = file_get_contents($dir.'/'.$file);
            $parts = preg_split('/={5}(.+?)={5}/', $fileData, null, PREG_SPLIT_DELIM_CAPTURE);
            array_shift($parts);

            $normalizedParts = array();
            for($i = 1; $i < count($parts); $i+=2){
                $normalizedParts[$i] = array(
                    'pattern' => $parts[$i-1],
                    'data' => $parts[$i]
                );
            }

            $fields = array('title', 'announce', 'content', 'address', 'russiaImageAnnounce');
            foreach($normalizedParts as $part){
                if(in_array($part['pattern'], $fields)) {
                    $method = 'set'.ucfirst($part['pattern']);
                    echo 'field "'.$part['pattern'].'" updated<br>';
                    $object->$method(trim($part['data']));
                }
            }

            $object->setPublished(true);
            $em->persist($object);
            $em->flush();
            echo "File: {$file} imported <br><br>";
        }
        closedir($dh);
        return new Response('Import finished');
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