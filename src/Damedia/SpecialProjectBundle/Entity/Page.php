<?php
namespace Damedia\SpecialProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Damedia\SpecialProjectBundle\Entity\Template;
use Damedia\SpecialProjectBundle\Entity\Block;
use Application\Sonata\MediaBundle\Entity\Media;
use Armd\NewsBundle\Entity\News;

/**
 * @ORM\Table(name="damedia_project_page")
 * @ORM\Entity(repositoryClass="Damedia\SpecialProjectBundle\Repository\PageRepository")
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
     * @ORM\JoinColumn(name="template", referencedColumnName="id")
     */
    private $template;

    /**
     * @ORM\OneToMany(targetEntity="Block", mappedBy="page")
     */
    protected $blocks;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\Column(name="stylesheet", type="text", nullable=true)
     */
    private $stylesheet;

    /**
     * @ORM\Column(name="javascript", type="text", nullable=true)
     */
    private $javascript;

    /**
     * @ORM\Column(name="show_on_main", type="boolean", nullable=true)
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
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="banner_image_id", referencedColumnName="id")
     */
    private $bannerImage;

    /**
     * @ORM\ManyToMany(targetEntity="Armd\NewsBundle\Entity\News", inversedBy="projects")
     * @ORM\JoinTable(name="sprojects_news")
     */
    private $news;



    public function __construct() {
        $this->blocks = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->news = new ArrayCollection();
    }



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

    public function __toString() {
        return $this->getTitle();
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

    public function getTemplate() {
        return $this->template;
    }
    public function setTemplate(Template $template) {
        $this->template = $template;

        return $this;
    }

    public function getBlocks() {
    	return $this->blocks;
    }
    public function addBlock(Block $blocks) {
        $this->blocks[] = $blocks;

        return $this;
    }
    public function removeBlock(Block $blocks) {
        $this->blocks->removeElement($blocks);
    }

    public function getParent() {
    	return $this->parent;
    }
    public function setParent(Page $parent = null) {
        $this->parent = $parent;

        return $this;
    }

    public function getChildren() {
    	return $this->children;
    }
    public function addChildren(Page $children) {
        $this->children[] = $children;

        return $this;
    }
    public function removeChildren(Page $children) {
        $this->children->removeElement($children);
    }

    public function getStylesheet() {
        return $this->stylesheet;
    }
    public function setStylesheet($stylesheet) {
        $this->stylesheet = $stylesheet;

        return $this;
    }

    public function getJavascript() {
        return $this->javascript;
    }
    public function setJavascript($javascript) {
        $this->javascript = $javascript;

        return $this;
    }

    public function getShowOnMain() {
        return $this->showOnMain;
    }
    public function setShowOnMain($showOnMain) {
        $this->showOnMain = $showOnMain;

        return $this;
    }

    public function getShowOnMainFrom() {
        return $this->showOnMainFrom;
    }
    public function setShowOnMainFrom($showOnMainFrom) {
        $this->showOnMainFrom = $showOnMainFrom;

        return $this;
    }

    public function getShowOnMainTo() {
        return $this->showOnMainTo;
    }
    public function setShowOnMainTo($showOnMainTo) {
        $this->showOnMainTo = $showOnMainTo;

        return $this;
    }

    public function getBannerImage() {
        return $this->bannerImage;
    }
    public function setBannerImage(Media $bannerImage) {
        if (is_null($bannerImage) || $bannerImage->isUploaded()) {
            $this->bannerImage = $bannerImage;
        }

        return $this;
    }

    public function getNews() {
        return $this->news;
    }
    public function setNews(News $news) {
        $this->news[] = $news;

        return $this;
    }
    public function addNews(News $news) {
        $this->news[] = $news;

        return $this;
    }
    public function removeNews(News $news) {
        $this->news->removeElement($news);
    }
}