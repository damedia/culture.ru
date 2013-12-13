<?php
/*
 * (c) Pekin Denis <dpekin@armd.ru>
 */

namespace Armd\DCXBundle\Util;

class Url
{
    public static function init(){
        if(!defined('HTTP_URL_REPLACE')){
            define('HTTP_URL_REPLACE',          0x0001);    // Replace every part of the first URL when there's one of the second URL    
        }
        if(!defined('HTTP_URL_JOIN_PATH')){
            define('HTTP_URL_JOIN_PATH',        0x0002);    // Join relative paths
        }
        if(!defined('HTTP_URL_JOIN_QUERY')){
            define('HTTP_URL_JOIN_QUERY',       0x0004);    // Join query strings
        }
        if(!defined('HTTP_URL_STRIP_USER')){
            define('HTTP_URL_STRIP_USER',       0x0008);    // Strip any user authentication information
        }
        if(!defined('HTTP_URL_STRIP_PASS')){
            define('HTTP_URL_STRIP_PASS',       0x0010);    // Strip any password authentication information
        }
        if(!defined('HTTP_URL_STRIP_PORT')){
            define('HTTP_URL_STRIP_PORT',       0x0020);    // Strip explicit port numbers
        }
        if(!defined('HTTP_URL_STRIP_PATH')){
            define('HTTP_URL_STRIP_PATH',       0x0040);    // Strip complete path
        }
        if(!defined('HTTP_URL_STRIP_QUERY')){
            define('HTTP_URL_STRIP_QUERY',      0x0080);    // Strip query string
        }
        if(!defined('HTTP_URL_STRIP_FRAGMENT')){
            define('HTTP_URL_STRIP_FRAGMENT',   0x0100);    // Strip any fragments (#identifier)
        }
        if(!defined('HTTP_URL_STRIP_AUTH')){
            define('HTTP_URL_STRIP_AUTH',       HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS);
        }
        if(!defined('HTTP_URL_STRIP_ALL')){
            define('HTTP_URL_STRIP_ALL',        HTTP_URL_STRIP_AUTH | HTTP_URL_STRIP_PORT | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);
        }
    }


    public static function build_url($url, $parts = array(), $flags = HTTP_URL_REPLACE, &$new_url = false)
    {
        if(!function_exists('http_build_url'))
        {
            if(is_string($url))
            {
                $url = parse_url($url);
            }

            if(is_string($parts))
            {
                $parts  = parse_url($parts);
            }

            if(isset($parts['scheme'])) $url['scheme']  = $parts['scheme'];
            if(isset($parts['host']))   $url['host']    = $parts['host'];

            if(HTTP_URL_REPLACE & $flags)
            {
                foreach(array('user','pass','port','path','query','fragment') as $key)
                {
                    if(isset($parts[$key])) $url[$key]  = $parts[$key];
                }
            }
            else
            {
                if(isset($parts['path']) && (HTTP_URL_JOIN_PATH & $flags))
                {
                    if(isset($url['path']) && $url['path'] != '')
                    {
                        if($url['path'][0] != '/')
                        {
                            if('/' == $parts['path'][strlen($parts['path'])-1])
                            {
                                $sBasePath  = $parts['path'];
                            }
                            else
                            {
                                $sBasePath  = dirname($parts['path']);
                            }

                            if('' == $sBasePath)    $sBasePath  = '/';

                            $url['path']    = $sBasePath . $url['path'];

                            unset($sBasePath);
                        }

                        if(false !== strpos($url['path'], './'))
                        {
                            while(preg_match('/\w+\/\.\.\//', $url['path'])){
                                $url['path']    = preg_replace('/\w+\/\.\.\//', '', $url['path']);
                            }

                            $url['path']    = str_replace('./', '', $url['path']);
                        }
                    }
                    else
                    {
                        $url['path']    = $parts['path'];
                    }
                }

                if(isset($parts['query']) && (HTTP_URL_JOIN_QUERY & $flags))
                {
                    if (isset($url['query']))   $url['query']   .= '&' . $parts['query'];
                    else                        $url['query']   = $parts['query'];
                }
            }

            if(HTTP_URL_STRIP_USER & $flags)        unset($url['user']);
            if(HTTP_URL_STRIP_PASS & $flags)        unset($url['pass']);
            if(HTTP_URL_STRIP_PORT & $flags)        unset($url['port']);
            if(HTTP_URL_STRIP_PATH & $flags)        unset($url['path']);
            if(HTTP_URL_STRIP_QUERY & $flags)       unset($url['query']);
            if(HTTP_URL_STRIP_FRAGMENT & $flags)    unset($url['fragment']);

            $new_url    = $url;

            return
                 ((isset($url['scheme'])) ? $url['scheme'] . '://' : '')
                .((isset($url['user'])) ? $url['user'] . ((isset($url['pass'])) ? ':' . $url['pass'] : '') .'@' : '')
                .((isset($url['host'])) ? $url['host'] : '')
                .((isset($url['port'])) ? ':' . $url['port'] : '')
                .((isset($url['path'])) ? $url['path'] : '')
                .((isset($url['query'])) ? '?' . $url['query'] : '')
                .((isset($url['fragment'])) ? '#' . $url['fragment'] : '')
            ;
        }
        else
        {
            return http_build_url($url, $parts, $flags, $new_url);
        }
    }
}