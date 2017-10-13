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

    /**
     * @var string
     */
    private $requestMethod;

    /**
     * @var string
     */
    private $request_data;

    /**
     * @var string
     */
    private $request_url;

    /**
     * @var string
     */
    private $request_port;

    /**
     * @var string
     */
    private $response_code;

    /**
     * @var string
     */
    private $response_content;

    /**
     * @var string
     */
    private $response_error;

    /**
     * @var string
     */
    private $response_header;

    /**
     * @var string
     */
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

    /**
     * @var string[]
     */
    private static $xml_format = array(self::FORMAT_XML, self::FORMAT_XML_DEPRECATED);

    /**
     * @param string $type
     *
     * @return \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestResult
     */
    public static function factory($type = self::TYPE_PLAIN)
    {
        $rest_result_class = __NAMESPACE__ . '\Result\\' .
             StringUtilities::getInstance()->createString($type)->upperCamelize();
        return new $rest_result_class();
    }

    /**
     * @param string $content_type
     *
     * @return \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestResult
     */
    public static function content_type_factory($content_type)
    {
        switch ($content_type)
        {
            case in_array($content_type, self::$xml_format) :
                $type = self::TYPE_XML;
                break;
            case self::FORMAT_HTML :
                $type = self::TYPE_HTML;
                break;
            case self::FORMAT_JSON :
                $type = self::TYPE_JSON;
                break;
            case self::FORMAT_PLAIN :
                $type = self::TYPE_PLAIN;
                break;
            default :
                $type = self::TYPE_PLAIN;
        }
        return self::factory($type);
    }

    /**
     * @return string
     */
    public function get_request_method()
    {
        return $this->requestMethod;
    }

    /**
     * @param string $request_method
     */
    public function set_request_method($request_method)
    {
        $this->requestMethod = $request_method;
    }

    /**
     * @return string
     */
    public function get_request_data()
    {
        return $this->request_data;
    }

    /**
     * @param string $request_data
     */
    public function set_request_data($request_data)
    {
        $this->request_data = $request_data;
    }

    /**
     * @return string
     */
    public function get_request_url()
    {
        return $this->request_url;
    }

    /**
     * @param string $request_url
     */
    public function set_request_url($request_url)
    {
        $this->request_url = $request_url;
    }

    /**
     * @return string
     */
    public function get_request_port()
    {
        return $this->request_port;
    }

    /**
     * @param string $request_port
     */
    public function set_request_port($request_port)
    {
        $this->request_port = $request_port;
    }

    /**
     * @return string
     */
    public function get_response_code()
    {
        return $this->response_code;
    }

    /**
     * @param string $response_code
     */
    public function set_response_code($response_code)
    {
        $this->response_code = $response_code;
    }

    /**
     * @param bool $parse
     *
     * @return string
     */
    public function get_response_content($parse = true)
    {
        return $this->response_content;
    }

    /**
     * @param string  $response_content
     */
    public function set_response_content($response_content)
    {
        $this->response_content = $response_content;
    }

    /**
     * @return string
     */
    public function get_response_error()
    {
        return $this->response_error;
    }

    /**
     * @param string $response_error
     */
    public function set_response_error($response_error)
    {
        $this->response_error = $response_error;
    }

    /**
     * @return string
     */
    public function get_response_header()
    {
        return $this->response_header;
    }

    /**
     * @param string $response_header
     */
    public function set_response_header($response_header)
    {
        $this->response_header = $response_header;
    }

    /**
     * @return string
     */
    public function get_response_cookies()
    {
        return $this->response_cookies;
    }

    /**
     * @param string $response_cookies
     */
    public function set_response_cookies($response_cookies)
    {
        $this->response_cookies = $response_cookies;
    }
}
