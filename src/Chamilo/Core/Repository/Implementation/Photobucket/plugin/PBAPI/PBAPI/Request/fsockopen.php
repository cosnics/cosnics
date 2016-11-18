<?php
/**
 * Photobucket API Fluent interface for PHP5 fsockopen request method
 * 
 * @author Photobucket
 * @package PBAPI
 * @subpackage Request
 * @copyright Copyright Copyright (c) 2008, Photobucket, Inc.
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Load Request parent
 */
require_once dirname(__FILE__) . '/../Request.php';
/**
 * fsockopen request strategy requires ability to use fsockopen
 * 
 * @package PBAPI
 * @subpackage Request
 */
class PBAPI_Request_fsockopen extends PBAPI_Request
{

    /**
     * Do actual request
     * 
     * @param $method string
     * @param $uri string
     * @param $params array
     * @return string
     */
    protected function request($method, $uri, $params = array())
    {
        $url = $this->preRequest($method, $uri, $params);
        $parts = parse_url($url);
        
        $len = 0;
        $data = '';
        $resp = '';
        
        // open socket
        if ($fp = @fsockopen($parts['host'], 80))
        {
            
            // generate request headers
            $path = (! empty($parts['path'])) ? $parts['path'] : '';
            $query = (! empty($parts['query'])) ? '?' . $parts['query'] : '';
            fputs($fp, "$method $path$query HTTP/1.1\n");
            fputs($fp, "Host: {$parts['host']}\n");
            fputs($fp, 'User-Agent: ' . __CLASS__ . "\n");
            
            // generate request headers for post
            if ($method == 'POST')
            {
                if (self :: detectFileUploadParams($params))
                {
                    $boundary = uniqid('xx');
                    $data = self :: multipartEncodeParams($this->oauth_request->getParameters(), $boundary);
                    fputs($fp, "Content-Type: multipart/form-data; boundary=$boundary\n");
                }
                else
                {
                    $data = $this->oauth_request->toPostdata();
                    fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
                }
                
                $len = strlen($data);
                fputs($fp, "Content-length: $len\n\n");
                fputs($fp, "$data\n");
            }
            
            // put last newline to signal i'm done
            fputs($fp, "\n");
            
            $headers = true;
            while (! feof($fp))
            {
                $line = fgets($fp); // get lines
                if (trim($line) == '')
                    $headers = false; // empty line will signal that we're done with headers
                else 
                    if (! $headers)
                        $resp .= $line; // dont capture headers to response
            }
            
            // close socket
            fclose($fp);
        }
        else
        {
            throw new PBAPI_Exception('FSOCKOPEN failed'); // todo exception
        }
        
        return $resp;
    }
}
