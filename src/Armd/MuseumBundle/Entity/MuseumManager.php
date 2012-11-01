<?php

namespace Armd\MuseumBundle\Entity;

use Doctrine\ORM\EntityManager;
use Armd\SphinxSearchBundle\Services\Search\SphinxSearch;
use Knp\Component\Pager\Paginator;

class MuseumManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    protected $paginator;
    protected $search;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Knp\Component\Pager\Paginator $paginator
     * @param \Armd\SphinxSearchBundle\Services\Search\SphinxSearch $search
     */
    public function __construct(EntityManager $em, Paginator $paginator, SphinxSearch $search)
    {
        $this->em = $em;
        $this->paginator = $paginator;
        $this->search = $search;
    }
    
    public function getPager(array $criteria, $page = 1, $perPage = 10)
    {
        if (empty($criteria['search_string'])) {

            $museumQb = $this->getQueryBuilder('m', $criteria)->getQuery();
            $pagination = $this->paginator->paginate($museumQb, $page, $perPage);

        } else {

            $searchParams = array(
                'Museums' => array(
                    'result_offset' => ($page - 1) * $perPage,
                    'result_limit' => $perPage,
                    'sort_mode' => '@relevance DESC, @weight DESC'
                )
            );

            $searchResult = $this->search->search($criteria['search_string'], $searchParams);
            $museums = array();
            if (!empty($searchResult['Museums']['matches'])) {
                foreach ($searchResult['Museums']['matches'] as $id => $data) {
                    $museum = $this->em->getRepository('ArmdMuseumBundle:Museum')->find($id);
                    if (!empty($museum)) {
                        $museums[] = $museum;
                    }
                }
            }
            $pagination = $this->paginator->paginate($museums, $page, $perPage);
            $pagination->setTotalItemCount($searchResult['Museums']['total']);

        }

        return $pagination;
    }
    
    public function getQueryBuilder($rootAlias, $criteria)
    {
        $query = $this->em->getRepository('ArmdMuseumBundle:Museum')->createQueryBuilder($rootAlias)
            ->addSelect($rootAlias . ', _i')
            ->leftJoin($rootAlias . '.image', '_i', 'WITH', '_i.enabled = true')
            ->andWhere($rootAlias . '.published = true')
            ->orderBy($rootAlias . '.title')
        ;
                
        return $query;
    }

}
