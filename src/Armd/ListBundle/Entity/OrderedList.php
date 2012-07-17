<?php

namespace Armd\ListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\ListBundle\Model\OrderedList as AbstractList;

/**
 * @ORM\MappedSuperclass
 */
class OrderedList extends AbstractList
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
