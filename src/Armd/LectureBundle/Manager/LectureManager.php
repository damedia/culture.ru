<?php
namespace Armd\LectureBundle\Manager;

use Doctrine\ORM\EntityManager;
use Armd\LectureBundle\Entity\LectureSuperType;
use Armd\SphinxSearchBundle\Services\Search\SphinxSearch;
use Knp\Component\Pager\Paginator;

class LectureManager
{
    private $em;
    private $paginator;
    private $search;

    public function __construct(EntityManager $em, Paginator $paginator, SphinxSearch $search)
    {
        $this->em = $em;
        $this->paginator = $paginator;
        $this->search = $search;
    }

    public function findFiltered($superType = null, $page = 1, $perPage = 20, $typeIds = null, $categoryIds = null, $sortBy = 'date', $searchString = '')
    {
        $lectureRepo = $this->em->getRepository('ArmdLectureBundle:Lecture');

        $searchParams = array(
            'Lectures' => array(
                'result_offset' => ($page - 1) * $perPage,
                'result_limit' => (int) $perPage,
                'sort_mode' => '@relevance DESC, @weight DESC, date_from DESC',
            )
        );

        // add filters
        if (isset($superType)) {
            $searchParams['Lectures']['filters'] = array(
                array(
                    'attribute' => 'lecture_super_type_id',
                    'values' => array($superType->getId())
                )
            );
        }

        // search by text with Sphinx
        if (is_array($typeIds)) {
            $searchParams['Lectures']['filters'][] = array(
                'attribute' => 'lecture_type_id',
                'values' => $typeIds
            );
        }

        if (is_array($categoryIds)) {
            $searchParams['Lectures']['filters'][] = array(
                'attribute' => 'lecturecategory_id',
                'values' => $categoryIds
            );
        }

        $searchResult = $this->search->search($searchString, $searchParams);

        $lectures = array();
        if (!empty($searchResult['Lectures']['matches'])) {
            $lectures = $lectureRepo->findBy(array('id' => array_keys($searchResult['Lectures']['matches'])));
        }

        return $lectures;

        $pagination = $this->paginator->paginate($lectures, $page, $perPage);
        $pagination->setTotalItemCount($searchResult['Lectures']['total']);

        return $pagination;
    }

    public function getStructuredRolesPersons(\Armd\LectureBundle\Entity\Lecture $lecture)
    {
        $res = array();
        foreach ($lecture->getRolesPersons() as $rp) {
            $res[$rp->getRole()->getName()][] = $rp->getPerson();
        }

        return $res;
    }

}