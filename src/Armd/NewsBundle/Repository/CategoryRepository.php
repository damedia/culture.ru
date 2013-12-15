<?php

namespace Armd\NewsBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository {
    public function findAll() {
        return $this->findBy(array(), array('priority' => 'ASC'));
    }
}
