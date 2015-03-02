<?php
namespace Chamilo\Libraries\File\Rss\Reader\Implementation;

/**
 * Wrapper for the RssPhp to extend some functionality - When updating the plugin, make sure to set the method
 * randomContext to protected
 * 
 * @package common\libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RssPhpWrapper extends \rss_php
{

    /**
     * Creates a random context to retrieve the rss feeds
     * 
     * @return resource
     */
    protected function randomContext()
    {
        $headerstrings = array();
        
        $headerstrings['User-Agent'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.' . rand(0, 2) . '; en-US; rv:1.' .
             rand(2, 9) . '.' . rand(0, 4) . '.' . rand(1, 9) . ') Gecko/2007' . rand(10, 12) . rand(10, 30) .
             ' Firefox/2.0.' . rand(0, 1) . '.' . rand(1, 9);
        $headerstrings['Accept-Charset'] = rand(0, 1) ? 'en-gb,en;q=0.' . rand(3, 8) : 'en-us,en;q=0.' . rand(3, 8);
        $headerstrings['Accept-Language'] = 'en-us,en;q=0.' . rand(4, 6);
        $headerstrings['Accept-Encoding'] = 'deflate';
        
        $setHeaders = 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5' .
             "\r\n" . 'Accept-Charset: ' . $headerstrings['Accept-Charset'] . "\r\n" . 'Accept-Language: ' .
             $headerstrings['Accept-Language'] . "\r\n" . 'User-Agent: ' . $headerstrings['User-Agent'] . "\r\n";
        $contextOptions = array('http' => array('method' => "GET", 'header' => $setHeaders));
        
        return stream_context_create($contextOptions);
    }
}