<?php

namespace Armd\UtilBundle\NestedSet;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;

class TreeRepairer
{

    public function rebuildTree(EntityManager $em, EntityRepository $er)
    {
        $em->beginTransaction();

        // Leveling the playing field.
        $er->createQueryBuilder('t')
            ->update()
            ->set('t.lft', -1)
            ->set('t.rgt', -1)
            ->set('t.lvl', -1)
            ->getQuery()->execute();

        // Establishing starting numbers for all root elements.
        $rootElements = $er->createQueryBuilder('t')
            ->where('t.parent IS NULL')
            ->getQuery()
            ->getResult();

        $startId = 1;
        foreach($rootElements as $rootElement) {
            $rootElement->setLft($startId);
            $rootElement->setRgt($startId + 1);
            $rootElement->setLvl(0);
            $startId += 2;
        }
        $em->flush();

        // Numbering all child elements
        while(
            $er->createQueryBuilder('t')
                ->select('COUNT(t)')
                ->where('t.lft < 0')
                ->getQuery()
                ->getSingleScalarResult()
        ) {

            // Picking an unprocessed element which has a processed parent.
            $element = $er->createQueryBuilder('t')
                ->innerJoin('t.parent', 'p')
                ->where('t.lft < 0')
                ->andWhere('p.lft > 0')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();

            $currentLeft = $element->getParent()->getLft();

            // Shifting all elements to the right of the current element 2 to the right.
            $er->createQueryBuilder('t')
                ->update()
                ->set('t.rgt', 't.rgt + 2')
                ->where('t.rgt > ?1')->setParameter(1, $currentLeft)
                ->getQuery()->execute();

            $er->createQueryBuilder('t')
                ->update()
                ->set('t.lft', 't.lft + 2')
                ->where('t.lft > ?1')->setParameter(1, $currentLeft)
                ->getQuery()->execute();

            // Setting lft and rght values for current element.
            $er->createQueryBuilder('t')
                ->update()
                ->set('t.lft', ':lft')
                ->set('t.rgt', ':rgt')
                ->set('t.lvl', ':lvl')
                ->where('t.id = :id')
                ->setParameters(
                    array(
                        'lft' => $currentLeft + 1,
                        'rgt' => $currentLeft + 2,
                        'lvl' => $element->getParent()->getLvl() + 1,
                        'id' => $element->getId()
                    )
                )
                ->getQuery()->execute();

            $em->clear();
        }

        $em->commit();
    }

}