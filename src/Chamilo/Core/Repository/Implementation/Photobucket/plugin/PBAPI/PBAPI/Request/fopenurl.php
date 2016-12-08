<?php
/**
 * Photobucket API Fluent interface for PHP5 fopen url request method
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
 * FOPEN_URL request strategy requires fopen url wrappers
 * 
 * @package PBAPI
 * @subpackage Request
 */
class PBAPI_Request_fopenurl extends PBAPI_Request
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
        
        $params = $this->request_params;
        
        // setup context
        $params['http']['method'] = $method;
        if (empty($params['http']['user_agent']))
            $params['http']['user_agent'] = __CLASS__;
            
            // setup context for posts
        if ($method == 'POST')
        {
            if (self :: detectFileUploadParams($params))
            {
                $boundary = uniqid('xx');
                $params['http']['header'] = 'Content-Type: multipart/form-data; boundary=' . $boundary;
                $params['http']['content'] = self :: multipartEncodeParams(
                    $this->oauth_request->getParameters(), 
                    $boundary);
            }
            else
            {
                $params['http']['header'] = 'Content-Type: application/x-www-form-urlencoded';
                $params['http']['content'] = $this->oauth_request->toPostdata();
            }
        }
        
        $ctx = stream_context_create($params);
        $out = file_get_contents($url, false, $ctx);
        
        if ($out == false)
        {
            throw new PBAPI_Exception('FOPENURL failed'); // todo exception
        }
        return $out;
    }
}
