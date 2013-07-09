<?php

namespace Damedia\SpecialProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="damedia_project_template")
 * @ORM\Entity
 */
class Template {
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="twig_file_name", type="string", length=255)
     */
    private $twigFileName;



    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    public function getTwigFileName() {
        return $this->twigFileName;
    }
    public function setTwigFileName($twigFileName) {
        $this->twigFileName = $twigFileName;

        return $this;
    }
}
