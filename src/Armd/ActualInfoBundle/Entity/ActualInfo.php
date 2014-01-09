<?php
namespace Armd\ActualInfoBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="ActualInfoRepository")
 * @ORM\Table(name="actual_info")
 * @ORM\HasLifecycleCallbacks
 */
class ActualInfo
{
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $caption;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id", nullable=true)
     */
    private $video;

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
     * Set type
     *
     * @param string $type
     * @return ActualInfo
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * Set title
     *
     * @param string $title
     * @return ActualInfo
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set caption
     *
     * @param string $caption
     * @return ActualInfo
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return ActualInfo
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return ActualInfo
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return \Armd\TvigleVideoBundle\Entity\TvigleVideo
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $video
     * @return ActualInfo
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
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
     * @return ActualInfo
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
        return '' . $this->text;
    }

    public function getClassName()
    {
        return get_class($this);
    }

}