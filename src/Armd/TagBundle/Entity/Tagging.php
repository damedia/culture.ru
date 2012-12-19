<?php
namespace Armd\TagBundle\Entity;

use FPN\TagBundle\Entity\Tagging as BaseTagging;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use DoctrineExtensions\Taggable\Entity\Tag as BaseTag;

/**
 *
 *
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="tagging_idx", columns={"tag_id", "resource_type", "resource_id"})})
 * @ORM\Entity
 *
 */
class Tagging extends BaseTagging
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
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="tagging")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     **/
    protected $tag;

//    /**
//     * Get id
//     *
//     * @return integer
//     */
//    public function getId()
//    {
//        return $this->id;
//    }
//
//    /**
//     * Set tag
//     *
//     * @param \Armd\TagBundle\Entity\Tag $tag
//     * @return Tagging
//     */
//    public function setTag(BaseTag $tag = null)
//    {
//        $this->tag = $tag;
//
//        return $this;
//    }
//
//    /**
//     * Get tag
//     *
//     * @return \Armd\TagBundle\Entity\Tag
//     */
//    public function getTag()
//    {
//        return $this->tag;
//    }
}