<?php
namespace Armd\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Query\ResultSetMapping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\Mapping\ClassMetadata;

use Armd\NewsBundle\Entity\NewsManager;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class AdminController extends Controller
{
    /**
     * @Route("/statistics", name="armd_news_admin_statistics")
     */
    public function statisticsAction()
    {
        $em = $this->getDoctrine()->getManager();

        //--- previous week stats
        $previousWeekCount = $em->createQueryBuilder()
            ->select('COUNT(n)')
            ->from('ArmdNewsBundle:News', 'n')
            ->where('n.publishedAt >= :date_from')
            ->andWhere('n.publishedAt < :date_to')
            ->setParameters(array(
                'date_from' => new \DateTime('monday previous week'),
                'date_to' => new \DateTime('last monday')
            ))
            ->getQuery()
            ->getSingleScalarResult();
        ///--- /previous week stats

        //--- this week stats
        /** @var ClassMetadata $newsMetadata  */
        $newsMetadata = $em->getClassMetadata('ArmdNewsBundle:News');
        $tableName = $newsMetadata->getTableName();
        $publishedColumn = $newsMetadata->getColumnName('publishedAt');

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('stat_count', 'stat_count');
        $rsm->addScalarResult('stat_date', 'stat_date');

        $thisWeekStats = $em->createNativeQuery("
            SELECT COUNT(*) stat_count, date_trunc('day', $publishedColumn) stat_date
            FROM $tableName n
            WHERE $publishedColumn >= :date_from
            AND $publishedColumn < :date_to
            GROUP BY stat_date
            ORDER BY stat_date
            ", $rsm)
            ->setParameters(array(
                'date_from' => new \DateTime('monday this week'),
                'date_to' => new \DateTime('next monday')
            ))
            ->getResult();
        //--- /this week stats


        return $this->render(
            'ArmdNewsBundle:Admin:news_statistics.html.twig',
            array(
                'base_template' => $this->container->get('sonata.admin.pool')->getTemplate('layout'),
                'admin_pool'      => $this->container->get('sonata.admin.pool'),
                'previous_week_count' => $previousWeekCount,
                'this_week_stats' => $thisWeekStats
            )
        );

    }

    

    
    
    
}