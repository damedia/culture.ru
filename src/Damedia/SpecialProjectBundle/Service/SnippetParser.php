<?php
namespace Damedia\SpecialProjectBundle\Service;

use Symfony\Component\HttpKernel\Kernel;
use Doctrine\ORM\EntityManager;
use Damedia\SpecialProjectBundle\Service\NeighborsCommunicator;

class SnippetParser {
    private $em;
    private $communicator;

    public function __construct(EntityManager $entityManager, NeighborsCommunicator $neighborsCommunicator) {
        $this->em = $entityManager;
        $this->communicator = $neighborsCommunicator;
    }

    public function html_to_entities(&$html) {
        preg_match_all('/<input.*class=\"snippet\".*data-twig=\"(\{%.*%\})\".*\/>/i', $html, $matches);

        $tokensToReplace = $matches[0];
        $replacements = $matches[1];

        $html = str_replace($tokensToReplace, $replacements, $html);
    }

    public function entities_to_html(&$html) {
        $objects = array();

        preg_match_all('/\{%\srender\surl\(\'damedia_foreign_entity\',\s\{\s\'entity\':\s\'(\w+)\',\s\'itemId\':\s(\d+)\s\}\)\s%\}/i', $html, $matches);

        $tokensToReplace = array_unique($matches[0]);
        $entities = $matches[1];
        $identifiers = $matches[2];

        //get metadata from recognized tokens
        foreach ($tokensToReplace as $i => $token) {
            $entity = $entities[$i];
            $objectId = $identifiers[$i];

            if (!isset($objects[$entity])) {
                $objects[$entity] = array('identifiers' => array(), //we need this to use 'WHERE id IN(...)' SQL clause with comfort =)
                                          'replacementsMap' => array()); //!!!we don't need this 'map'; even if an object was inserted into a Block more than once!!!
            }

            $objects[$entity]['identifiers'][] = $objectId;
            $objects[$entity]['replacementsMap'][$objectId][] = $i;
        }

        $replacements = array();

        //fetch objects from DB
        foreach ($objects as $entity => $data) {
            $qb = $this->em->createQueryBuilder(); //this MUST be inside foreach cycle else results will be completely random =(
            $entityDesc = $this->communicator->getFriendlyEntity($entity);

            $qb->select('n.'.$entityDesc['idField'].' AS id, n.'.$entityDesc['titleField'].' AS title')
                ->from($entityDesc['class'], 'n')
                ->where($qb->expr()->in('n.'.$entityDesc['idField'], $objects[$entity]['identifiers']));
            $result = $qb->getQuery()->getArrayResult();

            foreach ($result as $row) {
                $substitute  = '<input class="snippet" disabled="disabled" type="button" ';
                $substitute .= 'value="Type: '.$entity.', ID: '.$row['id'].', Label: '.htmlentities($row['title']).'" ';
                $substitute .= 'data-twig="{% render url(\'damedia_foreign_entity\', { \'entity\': \''.$entity.'\', \'itemId\': '.$row['id'].' }) %}" />';

                //replace every object appearance in given HTML
                foreach ($objects[$entity]['replacementsMap'][$row['id']] as $i) {
                    $replacements[$i] = $substitute;
                }
            }
        }

        ksort($replacements);

        $html = str_replace($tokensToReplace, $replacements, $html);
    }
}
?>