<?php
namespace Armd\DCXBundle\DCX;

class DCXDocument
{
    private $address;
    private $story_documents;
    private $body;
    private $charcount;
    private $copyrightnotice;
    private $created;
    private $creator;
    private $datecreated;
    private $dateimported;
    private $doc_id;
    private $email;
    private $filename;
    private $files;
    private $hotfolder;
    private $importedby;
    private $iptc01090;
    private $iptc02000;
    private $latitude;
    private $lead;
    private $longitude;
    private $maincategory;
    private $modcount;
    private $modified;
    private $objectname;
    private $objectnumber;
    private $objecttype;
    private $owner;
    private $phone;
    private $pool_id;
    private $region;
    private $schedule;
    private $status;
    private $storytype;
    private $title;
    private $type;
    private $unique;
    private $uri;
    private $wordcount;

    public function __construct(array $fields)
    {
        foreach ($fields as $key => $value) {
            if (property_exists($this, $key)){

                if($key == 'latitude' || $key == 'longitude')
                {
                    $this->$key = floatval(str_replace(',','.',$value));
                }
                else
                {
                    $this->$key = $value;
                }
            }
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)){
            return $this->$name;
        }
    }

    public function checkFileName($type, $variant)
    {
        if(!empty($this->files)){
            foreach ($this->files as $file) {
                if($file->type === $type && $file->variant === $variant){
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    public function getImageFileData($type, $variant){
        if(!empty($this->files)){
            foreach ($this->files as $file) {
                if($file->type === $type && $file->variant === $variant){
                    return $file;
                }
            }
            return false;
        }
        return false;
    }
}

