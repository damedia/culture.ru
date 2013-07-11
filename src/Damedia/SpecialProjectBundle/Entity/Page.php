<?php
namespace Damedia\SpecialProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="damedia_project_page")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Page {
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
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @ORM\Column(name="is_published", type="boolean")
     */
    private $isPublished = false;

    /**
     * @ORM\ManyToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    private $templateId;



    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps() {
        $this->updated = new \DateTime();

        if (!$this->getCreated()) {
            $this->created = new \DateTime();
        }
    }



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

    public function getSlug() {
        return $this->slug;
    }
    public function setSlug($slug) {
        $this->slug = $slug;

        return $this;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getUpdated() {
        return $this->updated;
    }

    public function getIsPublished() {
        return $this->isPublished;
    }
    public function setIsPublished($slug) {
        $this->isPublished = $slug;

        return $this;
    }

    public function getTemplateId() {
        return $this->templateId;
    }
    public function setTemplateId($templateId) {
        $this->templateId = $templateId;

        return $this;
    }
}
