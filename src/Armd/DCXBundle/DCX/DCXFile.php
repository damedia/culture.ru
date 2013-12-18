<?php
namespace Armd\DCXBundle\DCX;

class DCXFile
{
    private $id;
    private $dev_id;
    private $doc_id;
    private $created;
    private $current;
    private $hash;
    private $mimetype;
    private $modcount;
    private $modified;
    private $size;
    private $subpath;
    private $type;
    private $variant;
    private $version;
    private $imagebits;
    private $imagecolorspace;
    private $imageheight;
    private $imageorientation;
    private $imagewidth;
    private $path;
    private $displayname;

    public function __construct(array $fields)
    {
        foreach ($fields as $key => $value) {
            if (property_exists($this, $key)){
                $this->$key = $value;       
            }
        }
        $this->generatePath();
    }

    public function __get($name)
    {
        if (property_exists($this, $name)){
            return $this->$name;
        }
    }

    private function generatePath()
    {
        $this->path = ($this->dev_id) ? $this->dev_id.'/'.$this->subpath : $this->subpath;
    }

}