<?php
namespace Armd\TagBundle\Entity;

use FPN\TagBundle\Entity\Tag as BaseTag;
use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\TagBundle\Entity\Tag
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="DoctrineExtensions\Taggable\Entity\TagRepository")
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
     * @ORM\Column(type="boolean", options={"default" = false})
     */        
    private $isTechnical = false;
    
    public function getIsTechnical()
    {
        return $this->isTechnical;
    }
    
    public function setIsTechnical($isTechnical)
    {
        $this->isTechnical = $isTechnical;
    }

//    /**
//     * Add tagging
//     *
//     * @param \Armd\TagBundle\Entity\Tagging $tagging
//     * @return Tag
//     */
//    public function addTagging(\Armd\TagBundle\Entity\Tagging $tagging)
//    {
//        $this->tagging[] = $tagging;
//
//        return $this;
//    }
//
//    /**
//     * Remove tagging
//     *
//     * @param \Armd\TagBundle\Entity\Tagging $tagging
//     */
//    public function removeTagging(\Armd\TagBundle\Entity\Tagging $tagging)
//    {
//        $this->tagging->removeElement($tagging);
//    }
//
//    /**
//     * Get tagging
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getTagging()
//    {
//        return $this->tagging;
//    }
}