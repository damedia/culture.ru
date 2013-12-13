<?php

namespace Armd\DCXBundle\DCX;

use Buzz\Browser;
use Armd\DCXBundle\DCX\DCXParser;
use Armd\DCXBundle\Util\Url;
use Armd\DCXBundle\Exception\ClientException;

/**
 * @param \Buzz\Browser $browser
 */
class DCXClient
{
    private $browser;
    private $host;
    private $channel = null;
    private $scheme = 'http';
    private $path_channels = '/dcx_mkrf/atom/';
    private $path_document = '/dcx_mkrf/atom/document/';
    private $file_path = '/device_mkrf/';
    private $pub_path = '/dcx_mkrf/atom/pubinfos';

    /**
     * @param \Buzz\Browser $browser
     * @param string $host %dcx.host%
     */   
    public function __construct(Browser $browser, $host)
    {
        $this->browser = $browser;
        $this->host = $host;
        Url::init();
    }

    /**
     * @param void
     * @return array objects DCXChannel
     */
    public function getListChannels()
    {
        $url = Url::build_url('',array('scheme' => $this->scheme, 'host' => $this->host, 'path' => $this->path_channels));
        $xml = $this->getFeed($url);
        $parser = new DCXParser();
        return $parser->parseListChannels($xml);
    }

    /**
     * @param string $title
     * @return object DCXChannel or false
     */
    public function searchChannel($title)
    {
        $channels = $this->getListChannels();
        if (empty($channels)){
            return false;
        }
        foreach ($channels as $key => $channel) {
            $channel_title = $channel->getTitle();
            if ($channel_title === $title){
                return $channel;
            }
        }
        return false;

    }

    public function getFile($subpath)
    {
        $url = Url::build_url('',array('scheme' => $this->scheme, 'host' => $this->host, 'path' => $this->file_path.$subpath));
        $file = $this->getFeed($url, true);
        return $file;
    }

    /**
     * @param object DCXChannel
     * @return void
     */
    public function setChannel(DCXChannel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Simple search to DCX
     * @param string $search 
     * @param int $search_limit
     * @param int $search_index_of 
     * @return object DCXListDocuments
     */
    public function searchDocs($search, $search_limit = 10, $search_index_of = 0)
    {
        return $this->getListDocuments($search, $search_limit, $search_index_of);
    }

    /**
     * Get list documents from DCX
     * @param int $limit 
     * @param int $index_of 
     * @return object DCXListDocuments
     */
    public function listDocs($limit, $index_of)
    {
        return $this->getListDocuments(null, $limit, $index_of);
    }

    /**
     * @param string $id
     * @return object DCXDocument or string $response if document not found
     */
    public function getDoc($id)
    {
        $url = Url::build_url('',array('scheme' => $this->scheme, 'host' => $this->host, 'path' => $this->path_document.$id));
        $content = $this->getFeed($url);
        if (is_array($content)){
            return $content['response'];
        }
        $parser = new DCXParser();
        return $parser->parseDocument($content);
    }

    /**
     * @param string $search
     * @param int $limit
     * @param int $index_of
     * @return object DCXListDocuments
     */
    private function getListDocuments($search = null, $limit, $index_of)
    {
        $url = $this->getUrlChannel();

        $page_params = array('startIndex' => $index_of, 'itemsPerPage' => $limit);

        if ($search != null){
            $simple_search = array("q[fulltext][]" => $search);
        }

        $query = http_build_query(isset($simple_search) ? array_merge($simple_search, $page_params) : $page_params);
        $url = URL::build_url($url, array('query'=>$query), HTTP_URL_JOIN_QUERY);
        $xml = $this->getFeed($url);
        $parser = new DCXParser();
        return $parser->parseListDocuments($xml);
    }


    /**
     * @param string $url
     * @return string content or array if status code 404
     */
    private function getFeed($url)
    {
        $response = $this->browser->get($url);
        $status_code = $response->getStatusCode();
        if ($status_code === 404){
            return array('status_code' => 404, 'response' => $response->getContent());    
        }
        if ($status_code !== 200) {
            throw new ClientException('Host returned status code:'.$response->getStatusCode());
        }
        return $response->getContent();
    }

    public function sendPublicate($doc_id, $date)
    {
        $content = $this->genaratePubInfoRequest($date);
        $query = http_build_query(array('q[doc_id]'=>$doc_id));
        $url = Url::build_url('',array('scheme' => $this->scheme, 'host' => $this->host, 'path' => $this->pub_path, 'query'=>$query));
        $header = array('Content-Type' => 'application/atom+xml;type=entry');
        $response = $this->browser->post($url,$header,$content);
    }

    /**
     * @param void
     * @return object DCXChannel
     */
    private function getChannel()
    {
        if ($this->channel == null){
            throw new ClientException('Сhannel is not set');
        }
        return $this->channel;
    }

    /**
     * @param void
     * @return string $url channel
     */ 
    private function getUrlChannel()
    {
        $channel = $this->getChannel();
        return $channel->getUrl();
    }

    private function genaratePubInfoRequest($date)
    {
        $xml_response = '<?xml version="1.0" encoding="UTF-8"?>
            <!-- pubinfo_create.xml -->
            <entry xmlns="http://www.w3.org/2005/Atom" xmlns:dcx="http://www.digicol.com/xmlns/dcx">
               <pubinfo xmlns="http://www.digicol.com/xmlns/dcx" id="" version="1.0">
                  <book></book>
                  <book_num></book_num>
                  <cover_date></cover_date>
                  <cover_display_date></cover_display_date>
                  <created></created>
                  <date>'.$date.'</date>
                  <ending_page></ending_page>
                  <ending_page_num></ending_page_num>
                  <identifier></identifier>
                  <info/>
                  <kill_date></kill_date>
                  <number></number>
                  <number_num></number_num>
                  <page></page>
                  <page_part></page_part>
                  <page_range></page_range>
                  <partpage></partpage>
                  <prod_path></prod_path>
                  <publication_id id="publication_russia-images"/>
                  <remark></remark>
                  <revision></revision>
                  <section_id id=""/>
                  <special_issue_id id=""/>
                  <starting_page></starting_page>
                  <starting_page_num></starting_page_num>
                  <status_id id="pubstatus-uploaded"></status_id>
                  <subsection_id id=""/>
                  <title></title>
                  <type_id id="pubtype-image"></type_id>
                  <uri></uri>
                  <volume></volume>
                  <volume_num></volume_num>
               </pubinfo>
            </entry>'
        ;
        return $xml_response;
    }
}