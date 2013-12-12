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

                if($key != 'files' && $key != 'story_documents'){
                    $this->$key = (string) $value;
                }
                else
                {
                    $this->$key = $value;
                }
                if($key == 'latitude' || $key == 'longitude'){
                    $this->$key = floatval(str_replace(',','.',$value));
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

    private function getAttachedBySlot($slot)
    {
        $AttacheDocuments = $this->story_documents;
        if (count($AttacheDocuments) == 0){
            return false;
        }
        $slot_images = array();
        foreach ($AttacheDocuments as $value) {
            if($value->slot === $slot && $value->variant == 'Образы России'){
                array_push($slot_images, $value);
            }
        }
        return $slot_images;
    }

    public function getPrimaryImage()
    {
        $primaryImageObj = $this->getAttachedBySlot('primarypicture');
        if ($primaryImageObj === false){
            return false;
        }
        return $primaryImageObj[0];
    }

    public function getGaleryImages()
    {
        return $this->getAttachedBySlot('gallery');
    }
}

