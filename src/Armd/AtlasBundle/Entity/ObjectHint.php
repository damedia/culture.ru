<?php
namespace Armd\AtlasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Table(name="atlas_object_hint")
 * @ORM\Entity
 */

class ObjectHint {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Object", inversedBy="objectHints")
     * @ORM\JoinColumn(name="object_id")
     */
    private $object;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     *
     */
    private $title;

    /**
     * @ORM\Column(name="hint_text", type="text", nullable=false)
     */
    private $hintText;


    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getHintText()
    {
        return $this->hintText;
    }

    public function getPagedHintText()
    {
        $pages = preg_split('~-{3,}~', $this->hintText);
        return $pages;
    }

    public function setHintText($hintText)
    {
        $this->hintText = $hintText;
    }

}