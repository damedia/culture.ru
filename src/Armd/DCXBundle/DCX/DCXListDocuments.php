<?php
namespace Armd\DCXBundle\DCX;
class DCXListDocuments 
{

    private $docs;
    private $total_results;
    private $start_index;
    private $items_per_page;

    public function __get($name)
    {
        if (property_exists($this, $name)){
            return $this->$name;
        }
    }

    public function setDocuments($docs_list)
    {
        if (is_array($docs_list)){
            $this->docs = $docs_list;
        }
    }

    public function setTotalResults($total)
    {
        $this->total_results = (int)$total;
    }
    
    public function setStartIndex($index)
    {
        $this->start_index = (int)$index;
    }

    public function setItemsPerPage($total)
    {
        $this->items_per_page = (int)$total;
    }

}

