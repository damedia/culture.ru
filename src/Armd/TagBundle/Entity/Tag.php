<?php
namespace Armd\TagBundle\Entity;

use FPN\TagBundle\Entity\Tag as BaseTag;
use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\TagBundle\Entity\Tag
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Tag extends BaseTag
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Tagging", mappedBy="tag", fetch="EAGER")
     **/
    protected $tagging;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tagging = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Add tagging
     *
     * @param \Armd\TagBundle\Entity\Tagging $tagging
     * @return Tag
     */
    public function addTagging(\Armd\TagBundle\Entity\Tagging $tagging)
    {
        $this->tagging[] = $tagging;
    
        return $this;
    }

    /**
     * Remove tagging
     *
     * @param \Armd\TagBundle\Entity\Tagging $tagging
     */
    public function removeTagging(\Armd\TagBundle\Entity\Tagging $tagging)
    {
        $this->tagging->removeElement($tagging);
    }

    /**
     * Get tagging
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTagging()
    {
        return $this->tagging;
    }
}