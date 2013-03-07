<?php

namespace Armd\ExhibitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\AtlasBundle\Entity\ObjectManager;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Buzz\Browser;
use Armd\AtlasBundle\Entity\Category;
use Armd\AtlasBundle\Entity\Object;
use Armd\AtlasBundle\Form\ObjectType;
use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    private $limit = 5;
            
    /**
     * @Route("list", name="armd_exhibit_list")
     * @Template("ArmdExhibitBundle:Default:exhibit_list.html.twig")
     */
    public function listAction()
    {       
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ArmdExhibitBundle:ArtObject')->findBy(
            array('published' => true), null, $this->limit
        );               

        return array(
            'entities' => $entities,           
        );
    }
}