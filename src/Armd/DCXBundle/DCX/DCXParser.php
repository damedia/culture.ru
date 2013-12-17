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

    private function baseParseDocument($xml)
    {
        $sxml = $this->xmlLoader($xml);
        $sxml->registerXPathNamespace('dcx', "http://www.digicol.com/xmlns/dcx");
        $sxml->registerXPathNamespace('ns', "http://www.w3.org/1999/xhtml");
        $documents = $sxml->xpath("//dcx:document");
        foreach ($documents as $doc) {
            $doc_fields['doc_id'] = $doc['id'];
            $doc_fields['body'] = $doc->body->asXml();
            $doc_fields['pool_id'] = $doc->xpath('dcx:pool_id/@id');
            $doc_fields['created'] = $doc->xpath('dcx:created');
            $doc_fields['modcount'] = $doc->xpath('dcx:modcount');
            $doc_fields['modified'] = $doc->xpath('dcx:modified');
            $doc_fields['unique'] = $doc->xpath('dcx:unique');
            $doc_fields['type'] = $doc->xpath('dcx:head/dcx:Type');
            $doc_fields['objectname'] = $doc->xpath('dcx:head/dcx:ObjectName');
            $doc_fields['objecttype'] = $doc->xpath('dcx:head/dcx:ObjectType');
            $doc_fields['owner'] = $doc->xpath('dcx:head/dcx:Owner');
            $doc_fields['hotfolder'] = $doc->xpath('dcx:head/dcx:Hotfolder');
            $doc_fields['importedby'] = $doc->xpath('dcx:head/dcx:ImportedBy');
            $doc_fields['datecreated'] = $doc->xpath('dcx:head/dcx:DateCreated');
            $doc_fields['dateimported'] = $doc->xpath('dcx:head/dcx:DateImported');
            $doc_fields['filename'] = $doc->xpath('dcx:head/dcx:Filename');
            $doc_fields['title'] = $doc->xpath('dcx:head/dcx:Title');
            $doc_fields['status'] = $doc->xpath('dcx:head/dcx:Status');
            $doc_fields['storytype'] = $doc->xpath('dcx:head/dcx:StoryType');
            $doc_fields['creator'] = $doc->xpath('dcx:head/dcx:Creator');
            $doc_fields['region'] = $doc->xpath('dcx:head/dcx:Region');
            $doc_fields['lead'] = $doc->xpath('dcx:head/dcx:Lead');
            $doc_fields['uri'] = $doc->xpath('dcx:head/dcx:URI');
            $doc_fields['email'] = $doc->xpath('dcx:head/dcx:Email');
            $doc_fields['phone'] = $doc->xpath('dcx:head/dcx:Phone');
            $doc_fields['address'] = $doc->xpath('dcx:head/dcx:Address');
            $doc_fields['latitude'] = $doc->xpath('dcx:head/dcx:Latitude');
            $doc_fields['longitude'] = $doc->xpath('dcx:head/dcx:Longitude');
            $doc_fields['schedule'] = $doc->xpath('dcx:head/dcx:Schedule');
            $doc_fields['wordcount'] = $doc->xpath('dcx:head/dcx:WordCount');
            $doc_fields['charcount'] = $doc->xpath('dcx:head/dcx:CharCount');
            $doc_fields['wordcount'] = $doc->xpath('dcx:head/dcx:WordCount');
            $doc_fields['charcount'] = $doc->xpath('dcx:head/dcx:CharCount');
           
            $this->NormalizeSimpleXmlArray($doc_fields);
           
            $doc_fields['story_documents'] = array();
            $doc_fields['files'] = array();

            //files section
            $files = $doc->xpath('dcx:files/dcx:file');
            if ($files != false){
                foreach ($files as $f) {
                    $file['id'] = $f->xpath('@id');
                    $file['created'] = $f->xpath('dcx:created');
                    $file['current'] = $f->xpath('dcx:current');
                    $file['dev_id'] = $f->xpath('dcx:dev_id/@id');
                    $file['displayname'] = $f->xpath('dcx:displayname');
                    $file['doc_id'] = $f->xpath('dcx:doc_id/@id');
                    $file['hash'] = $f->xpath('dcx:hash');
                    $file['imagebits'] = $f->xpath('dcx:info/dcx:ImageBits');
                    $file['imagecolorspace'] = $f->xpath('dcx:info/dcx:ImageColorspace');
                    $file['imageheight'] = $f->xpath('dcx:info/dcx:ImageHeight');
                    $file['imageorientation'] = $f->xpath('dcx:info/dcx:ImageOrientation');
                    $file['imagewidth'] = $f->xpath('dcx:info/dcx:ImageWidth');
                    $file['mimetype'] = $f->xpath('dcx:mimetype');
                    $file['modcount'] = $f->xpath('dcx:modcount');
                    $file['modified'] = $f->xpath('dcx:modified');
                    $file['size'] = $f->xpath('dcx:size');
                    $file['subpath'] = $f->xpath('dcx:subpath');
                    $file['type'] = $f->xpath('dcx:type');
                    $file['variant'] = $f->xpath('dcx:variant');
                    $file['version'] = $f->xpath('dcx:version');
                    $file['doc_id'] = $f->xpath('dcx:doc_id/@id');
                    $this->NormalizeSimpleXmlArray($file);
                    array_push($doc_fields['files'], new DCXFile($file));
                    unset($file);
                }
            }

            //attached_to_story section
            $attached_to_story = $doc->xpath('dcx:attached_to_story/dcx:attached_document');
            if ($attached_to_story != false){
                foreach ($attached_to_story as $a) {
                    $attach['id'] = $a->xpath('@id');
                    $attach['type'] = $a->xpath('dcx:document_metadata/dcx:Type/@topic');
                    $attach['title'] = $a->xpath('dcx:document_metadata/dcx:Title');
                    $attach['fields_title'] = $a->xpath('dcx:fields/dcx:caption');
                    $attach['alt'] = $a->xpath('dcx:fields/dcx:alt');
                    $attach['byline'] = $a->xpath('dcx:fields/dcx:byline');
                    $attach['slot'] = $a->xpath('dcx:slot');
                    $attach['position'] = $a->xpath('dcx:slot/@position');
                    $attach['variant'] = $a->xpath('dcx:files/@variant');
                    $attach['href'] = $a->xpath("dcx:files/dcx:file[@type='original']/@src");
                    $this->NormalizeSimpleXmlArray($attach);
                    array_push($doc_fields['story_documents'], new DCXAttached($attach));
                    unset($attach);
                }
            }
            
            $docs[] = new DCXDocument($doc_fields);
            unset($doc_fields);
        }
        if (!isset($docs)){
            return false;
        }
        return $docs;
    }


    private function NormalizeSimpleXmlArray(&$array){
        array_walk($array, function(&$v){
                if ($v instanceof \SimpleXMLElement){
                    $v = $v->__toString();
                }
                if (is_array($v) && !empty($v)){
                    $v = $v[0]->__toString();
                }
                else if(is_array($v) && empty($v)){
                    $v = '';
                }

            } 
        );
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