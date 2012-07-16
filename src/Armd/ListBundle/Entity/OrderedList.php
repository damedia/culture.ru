<?php

namespace Armd\ListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\ListBundle\Model\OrderedList as BaseList;

/**
 * @ORM\MappedSuperclass
 */
class OrderedList extends BaseList
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
    
    /**
     * @ORM\Column(type="integer")
     */    
    protected $position;
}
