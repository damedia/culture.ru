<?php
namespace Armd\TagBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class TagController extends Controller{

    /**
     * @Route("/get-tags/", name="armd_tag_get_tags", options={"expose"=true})
     */
    public function getTagsAction() {

        $term = $this->getRequest()->get('term');

        $qb = $this->getDoctrine()->getManager()
            ->getRepository('ArmdTagBundle:Tag')
            ->createQueryBuilder('t')
            ->select('t.id, t.name')
            ->setMaxResults(30);

        if (!empty($term)) {
            $qb->where('t.name LIKE :term')->setParameter('term', "$term%");
        }

        $tags = $qb->getQuery()
            ->getScalarResult();

        return new JsonResponse($tags);

    }
}