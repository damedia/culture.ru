<?php
 
namespace Armd\DCXBundle\DCX;

class DCXChannel
{

    public function __construct(array $fields = array())
    {
        foreach ($fields as $key => $value) {
            if (property_exists($this, $key)){
                $this->$key = $value;       
            }
        }
    }
    
    private $href;
    private $title;
    private $accept;
    private $feed_type;


    public function getUrl()
    {
        return $this->href;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getAccept()
    {
        return $this->accept;
    }

    public function getFeedType()
    {
        return $this->feed_type;
    }
}
