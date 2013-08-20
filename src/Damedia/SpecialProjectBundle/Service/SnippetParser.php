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
        $replacements = array();

        preg_match_all('/\{%\srender\surl\(\'damedia_foreign_entity\',\s\{\s\'entity\':\s\'(\w+)\',\s\'itemId\':\s(\d+),\s\'view\':\s\'(\w+)\'\s\}\)\s%\}/i', $html, $matches);

        $tokensToReplace = array_unique($matches[0]);

        //get metadata from recognized tokens
        foreach ($tokensToReplace as $i => $token) {
            $entity = $matches[1][$i];
            $objectId = $matches[2][$i];
            $view = $matches[3][$i];

            $replacements[$i] = array('entity' => $entity, 'objectId' => $objectId, 'view' => $view);

            if (!isset($objects[$entity])) {
                $objects[$entity] = array(); //we need this to use 'WHERE id IN(...)' SQL clause with comfort :)
            }

            $objects[$entity][$objectId] = '';
        }

        //fetch objects from DB
        foreach ($objects as $entity => $data) {
            $qb = $this->em->createQueryBuilder(); //this MUST be inside foreach cycle else results will be completely random =(
            $entityDescription = $this->communicator->getFriendlyEntityDescription($entity);

            $qb->select('n.'.$entityDescription['idField'].' AS id, n.'.$entityDescription['titleField'].' AS title')
                ->from($entityDescription['class'], 'n')
                ->where($qb->expr()->in('n.'.$entityDescription['idField'], array_keys($objects[$entity])));
            $result = $qb->getQuery()->getArrayResult();

            foreach ($result as $row) {
                $objects[$entity][$row['id']] = $row['title'];
            }
        }

        //create replacements
        foreach ($replacements as $i => $data) {
            $label = htmlentities($objects[$data['entity']][$data['objectId']]);

            $substitute  = '<input class="snippet" disabled="disabled" type="button" ';
            $substitute .= 'value="Type: '.$data['entity'].', ID: '.$data['objectId'].', Label: '.$label.', View: '.$data['view'].'"';
            $substitute .= 'data-twig="{% render url(\'damedia_foreign_entity\', { \'entity\': \''.$data['entity'].'\', \'itemId\': '.$data['objectId'].', \'view\': \''.$data['view'].'\' }) %}" />';

            $replacements[$i] = $substitute;
        }

        $html = str_replace($tokensToReplace, $replacements, $html);
    }
}
?>