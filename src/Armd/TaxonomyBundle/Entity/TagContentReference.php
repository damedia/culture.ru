<?php

namespace Armd\TaxonomyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="taxonomy_tag_content_reference")
 */
class TagContentReference
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */    
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="entities")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")     
     */
    private $tag;
    
    /**
     * @ORM\ManyToOne(targetEntity="Armd\ContentAbstractBundle\Entity\Content")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id", onDelete="CASCADE")          
     */
    private $content;

    /**
     * @ORM\Column(type="boolean", name="is_personal")
     */    
    private $personal = false;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set personal
     *
     * @param boolean $personal
     */
    public function setPersonal($personal)
    {
        $this->personal = $personal;
    }

    /**
     * Get personal
     *
     * @return boolean 
     */
    public function getPersonal()
    {
        return $this->personal;
    }

    /**
     * Set tag
     *
     * @param Armd\TaxonomyBundle\Entity\Tag $tag
     */
    public function setTag(\Armd\TaxonomyBundle\Entity\Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Get tag
     *
     * @return Armd\TaxonomyBundle\Entity\Tag 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set content
     *
     * @param Armd\ContentAbstractBundle\Entity\Content $content
     */
    public function setContent(\Armd\ContentAbstractBundle\Entity\Content $content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return Armd\Bundle\CmsBundle\Entity\Content 
     */
    public function getContent()
    {
        return $this->content;
    }
}