<?php
/**
 * Photobucket API Fluent interface for PHP5 CURL request interface
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
 * CURL request strategy Requires CURL to be available and loaded
 * 
 * @package PBAPI
 * @subpackage Request
 */
class PBAPI_Request_curl extends PBAPI_Request
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
        
        $curl_opts = $this->request_params;
        
        // overridable
        if (empty($curl_opts[CURLOPT_USERAGENT]))
            $curl_opts[CURLOPT_USERAGENT] = __CLASS__;
            
            // static
        $curl_opts[CURLOPT_HEADER] = 0;
        $curl_opts[CURLOPT_FOLLOWLOCATION] = 1;
        $curl_opts[CURLOPT_RETURNTRANSFER] = 1;
        $curl_opts[CURLOPT_CUSTOMREQUEST] = $method;
        if ($method == 'POST')
        {
            $curl_opts[CURLOPT_POST] = 1;
            $curl_opts[CURLOPT_POSTFIELDS] = $params;
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, $curl_opts);
        $data = curl_exec($ch);
        
        if ($cerror = curl_errno($ch))
        {
            throw new PBAPI_Exception('CURL: ' . curl_error($ch), $cerror);
        }
        
        curl_close($ch);
        
        return $data;
    }
}
