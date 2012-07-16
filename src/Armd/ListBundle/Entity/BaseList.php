<?php

namespace Armd\ListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\ListBundle\Model\BaseList as AbstractList;

/**
 * @ORM\MappedSuperclass
 */
class BaseList extends AbstractList
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;    
}