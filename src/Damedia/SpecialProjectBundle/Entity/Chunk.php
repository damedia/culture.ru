<?php
namespace Damedia\SpecialProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class Chunk {
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Block")
     * @ORM\JoinColumn(name="block", referencedColumnName="id")
     */
    private $block;

    /**
     * @ORM\Column(name="content", type="text")
     */
    private $content;



    public function getId() {
        return $this->id;
    }

    public function getBlock() {
        return $this->block;
    }
    public function setBlock($block) {
        $this->block = $block;

        return $this;
    }

    public function getContent() {
        return $this->content;
    }
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }
}
