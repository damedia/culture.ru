<?php
namespace Damedia\SpecialProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
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
     * @ORM\ManyToOne(targetEntity="Page")
     * @ORM\JoinColumn(name="page", referencedColumnName="id")
     */
    private $page;

    /**
     * @ORM\Column(name="placeholder", type="string", length=255)
     */
    private $placeholder;



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
}
