<?php

namespace Armd\CommonBundle\Controller;

use Armd\ContentAbstractBundle\Controller\Controller;

class BaseController extends Controller
{
    /**
     * @param integer $id
     * @return mixed
     */    
    public function itemAction($id)
    {
        $entity = $this->getEntityRepository()->find($id);
            
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity');
        }

        return $this->renderCms(array(
            'entity'    => $entity,
            'related'   => $this->getRelatedEntities($id),
        ));        
    }
    
    public function getRelatedEntities($id)
    {
        $dql = "select
        	ce.name, cc.id
        from
        	{$this->getEntityName()} c,
        	ArmdTaxonomyBundle:TagContentReference ttcr,
        	ArmdTaxonomyBundle:TagContentReference ttcr2,
        	ArmdContentAbstractBundle:Content cc,
        	ArmdContentAbstractBundle:Entity ce        
        where
        	c.id = :id
        	and ttcr.content = c.content
            and ttcr.personal = 't'
            and ttcr2.tag = ttcr.tag
            and ttcr2.personal = 'f'
            and cc.id = ttcr2.content
            and ce.id = cc.entity
            and ce.id in (9, 10, 11, 12, 13, 14)            
        order by
            ce.name";
        	
        $query = $this->getDoctrine()->getEntityManager()->createQuery($dql)->setParameter('id', $id);
        $result = array();
        
        foreach($query->getResult() as $r)
        {
            $slugs = explode('\\',$r['name']);
            $item['usagetype'] = strtolower(array_pop($slugs));
            $item['entity'] = $this->getDoctrine()->getRepository($r['name'])->findOneBy(array('content' => $r['id']));
            $result[] = $item;
        }

        return $result;
    }
}
