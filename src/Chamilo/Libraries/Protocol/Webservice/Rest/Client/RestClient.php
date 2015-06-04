<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client;

use Chamilo\Libraries\Utilities\StringUtilities;

abstract class RestClient
{

    private $mode;

    private $base_url;

    private $method;

    private $data;

    private $data_mimetype;

    private $headers = array();

    private $endpoint;

    private $query_parameters;

    private $curl;

    private $authentication;

    private $check_target_certificate;

    private $target_ca_file;
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PUT = 'PUT';
    const MODE_PEAR = 'pear';
    const MODE_CURL = 'curl';

    public function __construct($base_url)
    {
        $this->set_base_url($base_url);
    }

    /**
     *
     * @return the $mode
     */
    public function get_mode()
    {
        return $this->mode;
    }

    /**
     *
     * @param $mode the $mode to set
     */
    public function set_mode($mode)
    {
        $this->mode = $mode;
    }

    /**
     *
     * @return the $base_url
     */
    public function get_base_url()
    {
        return $this->base_url;
    }

    /**
     *
     * @param $base_url the $base_url to set
     */
    public function set_base_url($base_url)
    {
        $this->base_url = $base_url;
    }

    /**
     *
     * @return the $method
     */
    public function get_method()
    {
        return $this->method;
    }

    /**
     *
     * @param $method the $method to set
     */
    public function set_method($method)
    {
        $this->method = $method;
    }

    /**
     *
     * @return the $data
     */
    public function get_data()
    {
        return $this->data;
    }

    /**
     *
     * @param $data the $data to set
     */
    public function set_data(RestData $data)
    {
        $this->data = $data;
    }

    public function has_data()
    {
        return isset($this->data);
    }

    /**
     *
     * @return the $data_mimetype
     */
    public function get_data_mimetype()
    {
        return $this->data_mimetype;
    }

    /**
     *
     * @param $data_mimetype the $data_mimetype to set
     */
    public function set_data_mimetype($data_mimetype)
    {
        $this->data_mimetype = $data_mimetype;
    }

    public function has_data_mimetype()
    {
        return isset($this->data_mimetype);
    }

    /**
     *
     * @return the $header
     */
    public function get_headers()
    {
        return $this->headers;
    }

    /**
     *
     * @param $header the $header to set
     */
    public function set_headers($headers)
    {
        $this->headers = $headers;
    }

    public function add_header($field, $value)
    {
        $this->headers[$field] = $value;
    }

    public function remove_header($field)
    {
        unset($this->headers[$field]);
    }

    /**
     *
     * @return the $endpoint
     */
    public function get_endpoint()
    {
        return $this->endpoint;
    }

    /**
     *
     * @param $endpoint the $endpoint to set
     */
    public function set_endpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     *
     * @return the $endpoint
     */
    public function get_query_parameters()
    {
        return $this->query_parameters;
    }

    /**
     *
     * @param $endpoint the $endpoint to set
     */
    public function set_query_parameters($query_parameters)
    {
        $this->query_parameters = $query_parameters;
    }

    public function has_query_parameters()
    {
        return (isset($this->query_parameters)) && (count($this->query_parameters) > 0);
    }

    public function get_resource_url()
    {
        $url = $this->base_url . $this->endpoint;
        if ($this->has_query_parameters())
        {
            $url .= '?' . http_build_query($this->query_parameters);
        }
        return $url;
    }

    public function add_query_parameter($parameter, $value)
    {
        $this->query_parameters[$parameter] = $value;
    }

    public function remove_query_parameter($parameter)
    {
        unset($this->query_parameters[$parameter]);
    }

    /**
     *
     * @param string $base_url
     * @param string $mode
     * @return \libraries\protocol\RestClient
     */
    public static function factory($base_url = '', $mode = self :: MODE_CURL)
    {
        $rest_client_class = __NAMESPACE__ . '\Client\\' .
             (string) StringUtilities :: getInstance()->createString($mode)->upperCamelize();
        return new $rest_client_class($base_url);
    }

    abstract public function request($response_mime_type = false);

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
    public function configure($method = self :: METHOD_GET, $endpoint = '', $query_parameters = array(), $data)
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
     *
     * @return the $authentication
     */
    public function get_authentication()
    {
        return $this->authentication;
    }

    /**
     *
     * @param $authentication the $authentication to set
     */
    public function set_authentication(RestAuthentication $authentication)
    {
        $this->authentication = $authentication;
    }

    public function has_authentication()
    {
        return isset($this->authentication);
    }

    public function get_check_target_certificate()
    {
        return $this->check_target_certificate;
    }

    public function set_check_target_certificate($check_target_certificate)
    {
        $this->check_target_certificate = $check_target_certificate;
    }

    public function get_target_ca_file()
    {
        return $this->target_ca_file;
    }

    public function set_target_ca_file($target_ca_file)
    {
        $this->target_ca_file = $target_ca_file;
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
