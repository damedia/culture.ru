<?php

namespace Armd\DCXBundle\DCX;

class DCXAttached
{
    private $alt;
    private $byline;
    private $fields_title;
    private $href;
    private $id;
    private $position;
    private $slot;
    private $title;
    private $type;
    private $variant;

    public function __construct(array $fields)
    {
        foreach ($fields as $key => $value) {
            if (property_exists($this, $key)){
                $this->$key = $value;       
            }
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)){
            return $this->$name;
        }
    }

}