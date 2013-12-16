<?php
namespace Armd\DCXBundle\DCX;

use Armd\DCXBundle\DCX\DCXChannel;
use Armd\DCXBundle\DCX\DCXDocument;
use Armd\DCXBundle\DCX\DCXListDocuments;
use Armd\DCXBundle\DCX\DCXFile;
use Armd\DCXBundle\DCX\DCXAttached;
use Armd\DCXBundle\Exception\ParserException;

class DCXParser
{

    /**
     * @param string $xml
     * @return array objects DCXChannel
     * 
     * @throws ParserException If simplexml_load_string() fires an error
     */
    public function parseListChannels($xml)
    {
        $sxml = $this->xmlLoader($xml);
        $sxml->registerXPathNamespace('ns', "http://www.w3.org/2007/app");
        $sxml->registerXPathNamespace('dcx', "http://www.digicol.com/xmlns/dcx");
        $sxml->registerXPathNamespace('atom', "http://www.w3.org/2005/Atom");
        $collection = $sxml->xpath("//ns:collection/dcx:feed_type[.='documents_channel']/parent::*");
        foreach ($collection as $ch) {
            $href = $ch['href'];
            $title = $sxml->xpath("//ns:collection[@href='$href']/atom:title");
            $feed_type = $sxml->xpath("//ns:collection[@href='$href']/dcx:feed_type");
            $ch = array('title'=>(string)$title[0],'href'=>(string)$href,'accept'=>(string)$ch->accept,'feed_type'=>(string)$feed_type[0]);
            $channels[] = new DCXChannel($ch);
        }
        return $channels;
    }

    /**
     * @param string $xml
     * @return object DCXListDocuments
     * 
     * @throws ParserException If simplexml_load_string() fires an error
     */
    public function parseListDocuments($xml)
    {
        $doc_list = $this->parseTotalResult($xml);
        if ($doc_list->tatal_results === 0){
            return $doc_list;
        }
        $docs = $this->baseParseDocument($xml);
        if ($docs !== false){
            $doc_list->setDocuments($docs);
        }
        return $doc_list;
    }
        
    /**
     * @param string $xml
     * @return object DCXDocument
     * 
     * @throws ParserException If simplexml_load_string() fires an error
     */
    public function parseDocument($xml)
    {
        $docs = $this->baseParseDocument($xml);
        return $docs[0];
    }

    //@todo пришлось делать в спешке, надо бы переписать и отрефакторить
    private function baseParseDocument($xml)
    {
        $sxml = $this->xmlLoader($xml);
        $sxml->registerXPathNamespace('dcx', "http://www.digicol.com/xmlns/dcx");
        $documents = $sxml->xpath("//dcx:document");
        foreach ($documents as $doc) {
            $doc_array['doc_id'] = (string)$doc['id'];
            foreach ($doc->children() as $d_key => $d_value) {
                $val = null;
                $val = ($d_key === 'body' && !array_key_exists($d_key, $doc_array)) && !isset($val) ?  (string) $d_value->asXml() : $val;
                $val = ($d_key === 'pool_id' && !array_key_exists($d_key, $doc_array) && !isset($val)) ?  (string) $d_value['id'] : $val;
                $val = ($d_key === 'head' && !array_key_exists($d_key, $doc_array) && !isset($val)) ? (array) $d_value->children()  : $val;
                $val = (!array_key_exists($d_key, $doc_array) && !isset($val)) ?  (string) $d_value : $val;
                if (!is_array($val)){
                    $doc_array[$d_key] = $val;
                }
                else{
                    if (!empty($val)) {
                        $val = array_combine(array_map(function($k){ return strtolower($k); }, array_keys($val)),$val);
                        $doc_array = array_merge($doc_array,$val);
                    }
                }
                $files = array();
                if ($d_key === 'files')
                {
                    foreach ($d_value as $f_value) {
                        $file['id'] = (string)$f_value['id'];
                        foreach ($f_value as $k => $v) {
                            $val = null;
                            $val = ($k === 'info' && !array_key_exists($k, $file) && !isset($val)) ? (array) $v->children()  : $val;
                            $val = ($k === 'dev_id' && !array_key_exists($k, $file) && !isset($val)) ? (string)$v['id'] : $val;
                            $val = ($k === 'doc_id' && !array_key_exists($k, $file) && !isset($val)) ? (string)$v['id'] : $val;
                            $val = (!array_key_exists($k, $file) && !isset($val)) ? (string)$v : $val;
                            if (!is_array($val)){
                                $file[$k] = $val;
                            }
                            else{
                                if (!empty($val)) {
                                    $val = array_combine(array_map(function($k){ return strtolower($k); }, array_keys($val)),$val);
                                    $file = array_merge($file,$val);
                                }
                            }
                        }
                        array_push($files,new DCXFile($file));
                        unset($file);
                    }
                    $doc_array['files'] = $files;
                }
                if ($d_key === 'attached_to_story'){
                    foreach ($d_value as $s_value) {
                        $attached['id'] = (string)$s_value['id'];
                        foreach ($s_value as $k => $v) {
                            $val = null;
                            $val = ($k === 'document_metadata' && !array_key_exists($k, $attached) && !isset($val)) ? (array) $v->children()  : $val;
                            $val = ($k === 'fields' && !array_key_exists($k, $attached) && !isset($val)) ? (array) $v->children()  : $val;
                            $val = ($k === 'slot' && !array_key_exists($k, $attached) && !isset($val)) ? array('slot'=>(string)$v, 'position'=>(string)$v['position']) : $val;
                            $val = ($k === 'files' && !array_key_exists($k, $attached) && !isset($val)) ? array('files'=>$v) : $val;
                            $val = (!array_key_exists($k, $attached) && !isset($val)) ? (string)$v : $val;
                            if (!is_array($val)){
                                $attached[$k] = $val;
                            }
                            else
                            {
                                foreach ($val as $nkey => $nvalue) {
                                    if ($nkey === 'Type'){
                                        $val[$nkey] = (string) $nvalue['topic'];
                                    }
                                    if($nkey === 'title' && is_object($nvalue)){
                                        unset($val[$nkey]);
                                    }
                                    if($nkey === 'caption'){
                                        $val['fields_title'] = $nvalue;
                                        unset($val[$nkey]);
                                    }
                                    if($nkey === 'files' && is_object($nvalue)){
                                        $val['variant'] = (string)$nvalue['variant'];
                                        $check = $nvalue->xpath("dcx:file[@type='original']");
                                        if (isset($check[0])){
                                            $href = $check[0]['src'];
                                        }
                                        else $href = '';
                                        $val['href'] = (string)$href;
                                    }
                                }
                                if (!empty($val)) {
                                    $val = array_combine(array_map(function($k){ return strtolower($k); }, array_keys($val)),$val);
                                    $attached = array_merge($attached,$val); 
                                }
                            }
                        }
                        array_push($files,new DCXAttached($attached));
                        unset($attached);
                    }
                    $doc_array['story_documents'] = $files;
                }
            }
            $docs[] = new DCXDocument($doc_array);
            unset($doc_array);
        }
        if (!isset($docs)){
            return false;
        }
        return $docs;
    }

    private function parseTotalResult($xml)
    {
        $doc_list = new DCXListDocuments();
        $sxml = $this->xmlLoader($xml);
        $sxml->registerXPathNamespace('os', "http://a9.com/-/spec/opensearch/1.1/");

        $total_result = $sxml->xpath("os:totalResults");
        $items_per_page = $sxml->xpath("os:itemsPerPage");
        $start_index = $sxml->xpath("os:startIndex");

        $doc_list->setTotalResults((string)$total_result[0]);
        $doc_list->setItemsPerPage((string)$items_per_page[0]);
        $doc_list->setStartIndex((string)$start_index[0]);

        return $doc_list;
    }

    /**
     * Wrapper simplexml_load_string
     * @param string $xml 
     * @return object SimpleXMLElement 
     */
    private function xmlLoader($xml){
        libxml_use_internal_errors(true);
        $sxml = simplexml_load_string($xml);
        if($sxml === false){
            throw new ParserException($xml);
        }
        return $sxml;
    }
}