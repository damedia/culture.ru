<?php
namespace Damedia\SpecialProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="damedia_project_block")
 * @ORM\Entity
 */
class Block {
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="blocks")
     * @ORM\JoinColumn(name="page", referencedColumnName="id")
     */
    private $page;

    /**
     * @ORM\Column(name="placeholder", type="string", length=255)
     */
    private $placeholder;
    
    /**
     * @ORM\OneToMany(targetEntity="Chunk", mappedBy="block")
     */
    protected $chunks;

    
    
    public function __construct() {
    	$this->chunks = new ArrayCollection();
    }
    


    public function __toString() {
        return (string)$this->getId();
    }



    public function getId() {
        return $this->id;
    }

    public function getPage() {
        return $this->page;
    }
    public function setPage($page) {
        $this->page = $page;

        return $this;
    }

    public function getPlaceholder() {
        return $this->placeholder;
    }
    public function setPlaceholder($placeholder) {
        $this->placeholder = $placeholder;

        return $this;
    }
    
    public function getChunks() {
    	return $this->chunks;
    }

    
    
    public function addChunk(\Damedia\SpecialProjectBundle\Entity\Chunk $chunks) {
        $this->chunks[] = $chunks;
    
        return $this;
    }
    public function removeChunk(\Damedia\SpecialProjectBundle\Entity\Chunk $chunks) {
        $this->chunks->removeElement($chunks);
    }
}