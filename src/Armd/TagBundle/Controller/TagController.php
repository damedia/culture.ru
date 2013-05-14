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

        $term = mb_strtolower($this->getRequest()->get('term'), 'UTF-8');

        $qb = $this->getDoctrine()->getManager()
            ->getRepository('ArmdTagBundle:Tag')
            ->createQueryBuilder('t')
            ->select('t.id, t.name')
            ->setMaxResults(30);

        if (!empty($term)) {
            $qb->where('LOWER(t.name) LIKE :term')->setParameter('term', "$term%");
        }

        $tags = $qb->getQuery()
            ->getScalarResult();

        return new JsonResponse($tags);

    }
}