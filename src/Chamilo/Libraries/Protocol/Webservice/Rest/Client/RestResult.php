<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Protocol\Webservice\Rest\Client
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class RestResult
{

    private $request_method;

    private $request_data;

    private $request_url;

    private $request_port;

    private $response_code;

    private $response_content;

    private $response_error;

    private $response_header;

    private $response_cookies;
    const TYPE_XML = 'xml';
    const TYPE_JSON = 'json';
    const TYPE_PLAIN = 'plain';
    const TYPE_HTML = 'html';
    const FORMAT_PLAIN = 'text/plain';
    const FORMAT_HTML = 'text/html';
    const FORMAT_JSON = 'application/json';
    const FORMAT_XML = 'application/xml';
    const FORMAT_XML_DEPRECATED = 'text/xml';

    private static $xml_format = array(self :: FORMAT_XML, self :: FORMAT_XML_DEPRECATED);

    public static function factory($type = self :: TYPE_PLAIN)
    {
        $rest_result_class = __NAMESPACE__ . '\Result\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize();
        return new $rest_result_class();
    }

    public static function content_type_factory($content_type)
    {
        switch ($content_type)
        {
            case in_array($content_type, self :: $xml_format) :
                $type = self :: TYPE_XML;
                break;
            case self :: FORMAT_HTML :
                $type = self :: TYPE_HTML;
                break;
            case self :: FORMAT_JSON :
                $type = self :: TYPE_JSON;
                break;
            case self :: FORMAT_PLAIN :
                $type = self :: TYPE_PLAIN;
                break;
            default :
                $type = self :: TYPE_PLAIN;
        }
        return self :: factory($type);
    }

    /**
     *
     * @return the $request_method
     */
    public function get_request_method()
    {
        return $this->request_method;
    }

    /**
     *
     * @param $request_method the $request_method to set
     */
    public function set_request_method($request_method)
    {
        $this->request_method = $request_method;
    }

    /**
     *
     * @return the $request_data
     */
    public function get_request_data()
    {
        return $this->request_data;
    }

    /**
     *
     * @param $request_data the $request_data to set
     */
    public function set_request_data($request_data)
    {
        $this->request_data = $request_data;
    }

    /**
     *
     * @return the $request_url
     */
    public function get_request_url()
    {
        return $this->request_url;
    }

    /**
     *
     * @param $request_url the $request_url to set
     */
    public function set_request_url($request_url)
    {
        $this->request_url = $request_url;
    }

    /**
     *
     * @return the $request_port
     */
    public function get_request_port()
    {
        return $this->request_port;
    }

    /**
     *
     * @param $request_port the $request_port to set
     */
    public function set_request_port($request_port)
    {
        $this->request_port = $request_port;
    }

    /**
     *
     * @return the $response_code
     */
    public function get_response_code()
    {
        return $this->response_code;
    }

    /**
     *
     * @param $response_code the $response_code to set
     */
    public function set_response_code($response_code)
    {
        $this->response_code = $response_code;
    }

    /**
     *
     * @return string
     */
    public function get_response_content($parse = true)
    {
        return $this->response_content;
    }

    /**
     *
     * @param $response_content the $response_content to set
     */
    public function set_response_content($response_content)
    {
        $this->response_content = $response_content;
    }

    /**
     *
     * @return the $response_error
     */
    public function get_response_error()
    {
        return $this->response_error;
    }

    /**
     *
     * @param $response_error the $response_error to set
     */
    public function set_response_error($response_error)
    {
        $this->response_error = $response_error;
    }

    /**
     *
     * @return the $response_header
     */
    public function get_response_header()
    {
        return $this->response_header;
    }

    /**
     *
     * @param $response_header the $response_header to set
     */
    public function set_response_header($response_header)
    {
        $this->response_header = $response_header;
    }

    /**
     *
     * @return the $response_cookies
     */
    public function get_response_cookies()
    {
        return $this->response_cookies;
    }

    /**
     *
     * @param $response_cookies the $response_cookies to set
     */
    public function set_response_cookies($response_cookies)
    {
        $this->response_cookies = $response_cookies;
    }
}
