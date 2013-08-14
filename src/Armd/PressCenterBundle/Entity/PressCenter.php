<?php
namespace Armd\PressCenterBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="PressCenterRepository")
 * @ORM\Table(name="press_center")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("slug")
 */
class PressCenter
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-z0-9-]+$/",
     *     match=true,
     *     message="Slug может содержать только латинские буквы, цифры и символ дефиса."
     * )
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\Column(name="show_on_main", type="boolean", nullable=false)
     */
    private $showOnMain = false;

    /**
     * @ORM\Column(name="show_on_main_from", type="datetime", nullable=true)
     */
    private $showOnMainFrom;

    /**
     * @ORM\Column(name="show_on_main_to", type="datetime", nullable=true)
     */
    private $showOnMainTo;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return PressCenter
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return PressCenter
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
     * Set content
     *
     * @param string $content
     * @return PressCenter
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return PressCenter
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
    {
        if (is_null($image) || $image->isUploaded()) {
            $this->image = $image;
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowOnMain()
    {
        return $this->showOnMain;
    }

    /**
     * @param $showOnMain boolean
     * @return $this
     */
    public function setShowOnMain($showOnMain)
    {
        $this->showOnMain = $showOnMain;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getShowOnMainFrom()
    {
        return $this->showOnMainFrom;
    }

    /**
     * @param $showOnMainFrom \DateTime
     * @return $this
     */
    public function setShowOnMainFrom($showOnMainFrom)
    {
        $this->showOnMainFrom = $showOnMainFrom;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getShowOnMainTo()
    {
        return $this->showOnMainTo;
    }

    /**
     * @param $showOnMainTo \DateTime
     * @return $this
     */
    public function setShowOnMainTo($showOnMainTo)
    {
        $this->showOnMainTo = $showOnMainTo;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '' . $this->title;
    }

    public function getClassName()
    {
        return get_class($this);
    }

}