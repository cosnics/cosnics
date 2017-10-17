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
     *
     * @var string
     */
    private $requestMethod;

    /**
     *
     * @var string
     */
    private $requestData;

    /**
     *
     * @var string
     */
    private $requestUrl;

    /**
     *
     * @var string
     */
    private $requestPort;

    /**
     *
     * @var string
     */
    private $responseCode;

    /**
     *
     * @var string
     */
    private $responseContent;

    /**
     *
     * @var string
     */
    private $responseError;

    /**
     *
     * @var string
     */
    private $responseHeader;

    /**
     *
     * @var string
     */
    private $responseCookies;
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
     *
     * @var string[]
     */
    private static $xml_format = array(self::FORMAT_XML, self::FORMAT_XML_DEPRECATED);

    /**
     *
     * @param string $type
     * @return \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestResult
     */
    public static function factory($type = self::TYPE_PLAIN)
    {
        $rest_result_class = __NAMESPACE__ . '\Result\\' .
             StringUtilities::getInstance()->createString($type)->upperCamelize();
        return new $rest_result_class();
    }

    /**
     *
     * @param string $contentType
     * @return \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestResult
     */
    public static function content_type_factory($contentType)
    {
        switch ($contentType)
        {
            case in_array($contentType, self::$xml_format) :
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
     *
     * @return string
     */
    public function get_request_method()
    {
        return $this->requestMethod;
    }

    /**
     *
     * @param string $requestMethod
     */
    public function set_request_method($requestMethod)
    {
        $this->requestMethod = $requestMethod;
    }

    /**
     *
     * @return string
     */
    public function get_request_data()
    {
        return $this->requestData;
    }

    /**
     *
     * @param string $requestData
     */
    public function set_request_data($requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     *
     * @return string
     */
    public function get_request_url()
    {
        return $this->requestUrl;
    }

    /**
     *
     * @param string $requestUrl
     */
    public function set_request_url($requestUrl)
    {
        $this->requestUrl = $requestUrl;
    }

    /**
     *
     * @return string
     */
    public function get_request_port()
    {
        return $this->requestPort;
    }

    /**
     *
     * @param string $requestPort
     */
    public function set_request_port($requestPort)
    {
        $this->requestPort = $requestPort;
    }

    /**
     *
     * @return string
     */
    public function get_response_code()
    {
        return $this->responseCode;
    }

    /**
     *
     * @param string $responseCode
     */
    public function set_response_code($responseCode)
    {
        $this->responseCode = $responseCode;
    }

    /**
     *
     * @param boolean $parse
     *
     * @return string
     */
    public function get_response_content($parse = true)
    {
        return $this->responseContent;
    }

    /**
     *
     * @param string $responseContent
     */
    public function set_response_content($responseContent)
    {
        $this->responseContent = $responseContent;
    }

    /**
     *
     * @return string
     */
    public function get_response_error()
    {
        return $this->responseError;
    }

    /**
     *
     * @param string $responseError
     */
    public function set_response_error($responseError)
    {
        $this->responseError = $responseError;
    }

    /**
     *
     * @return string
     */
    public function get_response_header()
    {
        return $this->responseHeader;
    }

    /**
     *
     * @param string $responseHeader
     */
    public function set_response_header($responseHeader)
    {
        $this->responseHeader = $responseHeader;
    }

    /**
     *
     * @return string
     */
    public function get_response_cookies()
    {
        return $this->responseCookies;
    }

    /**
     *
     * @param string $responseCookies
     */
    public function set_response_cookies($responseCookies)
    {
        $this->responseCookies = $responseCookies;
    }
}
