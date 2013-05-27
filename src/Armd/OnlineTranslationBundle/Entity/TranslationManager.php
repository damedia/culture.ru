<?php
namespace Armd\OnlineTranslationBundle\Entity;

use Doctrine\ORM\EntityManager;

class TranslationManager {
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function getActiveTranslation()
    {
        $entities = $this->em
            ->getRepository('ArmdOnlineTranslationBundle:OnlineTranslation')
            ->findBy(array('published' => 1), array('date' => 'DESC'), 1);

        if (isset($entities[0])) {
            $entity = $entities[0];
        } else {
            $entity = false;
        }

        return $entity;
    }

}