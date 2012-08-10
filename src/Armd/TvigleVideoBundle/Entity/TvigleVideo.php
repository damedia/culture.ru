<?php

namespace Armd\TvigleVideoBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tvigle_video")
 */
class TvigleVideo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $tvigleId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $swf;


    public function __toString()
    {
        return $this->getTitle() . ' (' . $this->getTvigleId() . ')';
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
     * Set tvigleId
     *
     * @param integer $tvigleId
     */
    public function setTvigleId($tvigleId)
    {
        $this->tvigleId = $tvigleId;
    }

    /**
     * Get tvigleId
     *
     * @return integer 
     */
    public function getTvigleId()
    {
        return $this->tvigleId;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set image
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    public function getDurationString()
    {
        $duration = $this->duration / 1000;
        $hours = floor($duration / 3600);
        $durationLeft = $duration % 3600;

        $mins = floor($durationLeft / 60);
        $secs = $durationLeft % 60;

        $durationString = '';
        if($hours > 0) {
            $durationString .= sprintf('%02d', $hours) . ':';
        }
        $durationString .= sprintf('%02d', $mins) . ':' . sprintf('%02d', $secs);

        return $durationString;
    }

//    /**
//     * Get duration as string
//     *
//     * @return string
//     */
//    public function getDurationAsString()
//    {
//        $hours = floor(($this->duration/1000) / (60*60));
//        $mins = floor((($this->duration/1000) - $hours*60*60 )/ 60);
//        $sec = floor((($this->duration/1000) - $hours*60*60 - $mins * 60 ));
//
//        if($hours) {
//            $res = sprintf("%s:%02s:%02s", $hours, $mins, $sec);
//        } elseif($mins) {
//            $res = sprintf("%02s:%02s", $hours, $mins, $sec);
//        } else {
//            $res = sprintf("%02s", $hours, $mins, $sec);
//        }
//
//        return $res;
//    }


    /**
     * Set created
     *
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set code
     *
     * @param text $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

//    /**
//     * Get code
//     *
//     * @return text
//     */
//    public function getCode()
//    {
//        return $this->code;
//    }
//
//    public function getResizedCode($width, $height) {
//       $code = preg_replace('~(width="?)(\d)("?)~', '$1' . $width . '$3', $this->code);
//       $code = preg_replace('~(height="?)(\d)("?)~', '$1' . $height . '$3', $code);
//       return $code;
//    }
//
    public function getSwf()
    {
        return $this->swf;
    }

    public function setSwf($swf)
    {
        $this->swf = $swf;
    }

}