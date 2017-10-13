<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client;

use Chamilo\Libraries\Utilities\StringUtilities;

abstract class RestClient
{

    /**
     * @var string
     */
    private $mode;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $method;

    /**
     * @var \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestData
     */
    private $data;

    /**
     * @var string
     */
    private $dataMimeType;

    /**
     * @var string[]
     */
    private $headers = array();

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string[]
     */
    private $queryParameters;

    /**
     * @var RestAuthentication
     */
    private $authentication;

    /**
     * @var string
     */
    private $checkTargetCertificate;

    /**
     * @var string
     */
    private $targetCAFile;

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PUT = 'PUT';
    const MODE_PEAR = 'pear';
    const MODE_CURL = 'curl';

    /**
     * RestClient constructor.
     *
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->set_base_url($baseUrl);
    }

    /**
     * @return string
     */
    public function get_mode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function set_mode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function get_base_url()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     */
    public function set_base_url($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return string
     */
    public function get_method()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function set_method($method)
    {
        $this->method = $method;
    }

    /**
     * @return \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestData
     */
    public function get_data()
    {
        return $this->data;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestData $data
     */
    public function set_data(RestData $data)
    {
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function has_data()
    {
        return isset($this->data);
    }

    /**
     * @return string
     */
    public function get_data_mimeType()
    {
        return $this->dataMimeType;
    }

    /**
     * @param string $dataMimeType
     */
    public function set_data_mimeType($dataMimeType)
    {
        $this->dataMimeType = $dataMimeType;
    }

    /**
     * @return bool
     */
    public function has_data_mimetype()
    {
        return isset($this->dataMimeType);
    }

    /**
     * @return string[]
     */
    public function get_headers()
    {
        return $this->headers;
    }

    /**
     * @param string[] $headers
     */
    public function set_headers($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $field
     * @param string $value
     */
    public function add_header($field, $value)
    {
        $this->headers[$field] = $value;
    }

    /**
     * @param string $field
     */
    public function remove_header($field)
    {
        unset($this->headers[$field]);
    }

    /**
     * @return string
     */
    public function get_endpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     */
    public function set_endpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function get_query_parameters()
    {
        return $this->queryParameters;
    }

    /**
     * @param string[] $queryParameters
     */
    public function set_query_parameters($queryParameters)
    {
        $this->queryParameters = $queryParameters;
    }

    /**
     * @return bool
     */
    public function has_query_parameters()
    {
        return (isset($this->queryParameters)) && (count($this->queryParameters) > 0);
    }

    /**
     * @return string
     */
    public function get_resource_url()
    {
        $url = $this->baseUrl . $this->endpoint;
        if ($this->has_query_parameters())
        {
            $url .= '?' . http_build_query($this->queryParameters);
        }
        return $url;
    }

    /**
     * @param string $parameter
     * @param string $value
     */
    public function add_query_parameter($parameter, $value)
    {
        $this->queryParameters[$parameter] = $value;
    }

    /**
     * @param string $parameter
     */
    public function remove_query_parameter($parameter)
    {
        unset($this->queryParameters[$parameter]);
    }

    /**
     * @param string $base_url
     * @param string $mode
     *
     * @return \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestClient
     */
    public static function factory($base_url = '', $mode = self::MODE_CURL)
    {
        $rest_client_class = __NAMESPACE__ . '\Client\\' .
             (string) StringUtilities::getInstance()->createString($mode)->upperCamelize();
        return new $rest_client_class($base_url);
    }

    /**
     * @param bool $responseMimeType
     *
     * @return mixed
     */
    abstract public function request($responseMimeType = false);

    /**
     * @param string $http_code
     *
     * @return string
     */
    public function get_http_code_translation($http_code)
    {
        switch ($http_code)
        {
            case '400' :
                return 'Bad Request';
            case '401' :
                return 'Unauthorized';
            case '402' :
                return 'Payment Required';
            case '403' :
                return 'Forbidden';
            case '404' :
                return 'Not Found';
            case '405' :
                return 'Method Not Allowed';
            case '406' :
                return 'Not Acceptable';
            case '407' :
                return 'Proxy Authentication Required';
            case '408' :
                return 'Request Time-out';
            case '409' :
                return 'Conflict';
            case '410' :
                return 'Gone';
            case '411' :
                return 'Length Required';
            case '412' :
                return 'Precondition Failed';
            case '413' :
                return 'Request Entity Too Large';
            case '414' :
                return 'Request-URI Too Long';
            case '415' :
                return 'Unsupported Media Type';
            case '416' :
                return 'Requested range unsatisfiable';
            case '417' :
                return 'Expectation failed';
            case '422' :
                return 'Unprocessable entity';
            case '423' :
                return 'Locked';
            case '424' :
                return 'Method failure';
            
            case '500' :
                return 'Internal Server Error';
            case '501' :
                return 'Not Implemented';
            case '502' :
                return 'Bad Gateway ou Proxy Error';
            case '503' :
                return 'Service Unavailable';
            case '504' :
                return 'Gateway Time-out';
            case '505' :
                return 'HTTP Version not supported';
            case '507' :
                return 'Insufficient storage';
            case '509' :
                return 'Bandwidth Limit Exceeded';
            
            default :
                return null;
        }
    }

    /**
     *
     * @param string $method
     * @param string $endpoint
     * @param string[] $query_parameters
     * @param RestData $data
     */
    public function configure($method = self::METHOD_GET, $endpoint = '', $query_parameters = array(), $data)
    {
        $this->set_method($method);
        $this->set_endpoint($endpoint);
        $this->set_query_parameters($query_parameters);
        
        if ($data)
        {
            $this->set_data($data);
        }
    }

    /**
     * @return \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestAuthentication
     */
    public function get_authentication()
    {
        return $this->authentication;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestAuthentication $authentication
     */
    public function set_authentication(RestAuthentication $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * @return bool
     */
    public function has_authentication()
    {
        return isset($this->authentication);
    }

    /**
     * @return string
     */
    public function get_check_target_certificate()
    {
        return $this->checkTargetCertificate;
    }

    /**
     * @param string $checkTargetCertificate
     */
    public function set_check_target_certificate($checkTargetCertificate)
    {
        $this->checkTargetCertificate = $checkTargetCertificate;
    }

    /**
     * @return string
     */
    public function get_target_ca_file()
    {
        return $this->targetCAFile;
    }

    /**
     * @param string $targetCAFile
     */
    public function set_target_ca_file($targetCAFile)
    {
        $this->targetCAFile = $targetCAFile;
    }

    /**
     *
     * @param $content_type string
     */
    public static function extract_mime_type($content_type)
    {
        $content_type_parts = explode(';', $content_type);
        return $content_type_parts[0];
    }
}
