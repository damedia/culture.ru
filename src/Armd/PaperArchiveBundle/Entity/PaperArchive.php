<?php

namespace Armd\PaperArchiveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="content_press_archive")
 * @ORM\HasLifecycleCallbacks
 */
class PaperArchive
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="datetime", name="date_from")
     */
    protected $date;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\PaperArchiveBundle\Entity\PaperEdition", cascade={"all"})
     * @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     */
    private $edition;

    private $previewFlag;

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
     * Set title
     *
     * @param string $title
     * @return PaperArchive
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return PaperArchive
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set image
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $image
     * @return PaperArchive
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return Application\Sonata\MediaBundle\Entity\Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set file
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $file
     * @return PaperArchive
     */
    public function setFile(\Application\Sonata\MediaBundle\Entity\Media $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return Application\Sonata\MediaBundle\Entity\Media
     */
    public function getFile()
    {
        return $this->file;
    }

    public function setPreviewFlag($flag)
    {
        $this->previewFlag = $flag;

        return $this;
    }

    public function getPreviewFlag()
    {
        return $this->previewFlag;
    }

    /**
     * Set edition
     *
     * @param Armd\PaperArchiveBundle\Entity\PaperEdition $edition
     * @return PaperArchive
     */
    public function setEdition(\Armd\PaperArchiveBundle\Entity\PaperEdition $edition = null)
    {
        $this->edition = $edition;
    
        return $this;
    }

    /**
     * Get edition
     *
     * @return Armd\PaperArchiveBundle\Entity\PaperEdition 
     */
    public function getEdition()
    {
        return $this->edition;
    }
}