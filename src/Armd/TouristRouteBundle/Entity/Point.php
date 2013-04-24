<?php

namespace Armd\TouristRouteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\TouristRouteBundle\Entity\Point
 *
 * @ORM\Table(name="tourist_route_point")
 * @ORM\Entity
 */
class Point
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(name="list_order", type="integer")
     */
    private $order = 0;

    /**
     * @ORM\Column(name="show", type="boolean")
     */
    private $show = true;

    /**
     * @ORM\Column(name="lat", type="decimal", precision=15, scale=10, nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(name="lon", type="decimal", precision=15, scale=10, nullable=true)
     */
    private $lon;


    public function __toString()
    {
        $title = $this->getTitle();

        return $title ? $title : '#' .$this->getId();
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
     * @return Point
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return Point
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get show
     *
     * @return bool
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * Set show
     *
     * @param bool $show
     * @return Point
     */
    public function setShow($show)
    {
        $this->show = $show;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lat
     *
     * @param float $lat
     * @return Point
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lon
     *
     * @return float
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Set lon
     *
     * @param float $lon
     * @return Point
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }
}