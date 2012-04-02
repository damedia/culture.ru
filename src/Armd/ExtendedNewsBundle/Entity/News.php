<?php

namespace Armd\ExtendedNewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\Bundle\NewsBundle\Entity\NewsMappedSuperclass as BaseNews;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_news_extended")
 */
class News extends BaseNews
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $source;

    /**
     * Set source
     *
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }
}