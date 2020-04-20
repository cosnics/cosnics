<?php
namespace Chamilo\Libraries\Architecture;

use Chamilo\Libraries\Translation\Translation;

/**
 * This class represents a default Json response as provided and used by the various AJAX calls throughout Chamilo
 *
 * @package Chamilo\Libraries\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class JsonAjaxResult
{

    /**
     *
     * @var string[]
     */
    private static $result_codes = array(
        100 => 'Continue',
        101 => 'SwitchingProtocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'NonAuthoritativeInformation',
        204 => 'NoContent',
        205 => 'ResetContent',
        206 => 'PartialContent',
        207 => 'MultiStatus',
        300 => 'MultipleChoices',
        301 => 'MovedPermanently',
        302 => 'Found',
        303 => 'SeeOther',
        304 => 'NotModified',
        305 => 'UseProxy',
        306 => 'SwitchProxy',
        307 => 'TemporaryRedirect',
        400 => 'BadRequest',
        401 => 'Unauthorized',
        402 => 'PaymentRequired',
        403 => 'Forbidden',
        404 => 'NotFound',
        405 => 'MethodNotAllowed',
        406 => 'NotAcceptable',
        407 => 'ProxyAuthenticationRequired',
        408 => 'RequestTimeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'LengthRequired',
        412 => 'PreconditionFailed',
        413 => 'RequestEntityTooLarge',
        414 => 'RequestURITooLong',
        415 => 'UnsupportedMediaType',
        416 => 'RequestedRangeNotSatisfiable',
        417 => 'ExpectationFailed',
        422 => 'UnprocessableEntity',
        423 => 'Locked',
        424 => 'FailedDependency',
        425 => 'UnorderdCollection',
        426 => 'UpgradeRequired',
        449 => 'RetryWith',
        450 => 'BlockedByWindowsParentalControls',
        499 => 'ClientClosedRequest',
        500 => 'InternalServerError',
        501 => 'NotImplemented',
        502 => 'BadGateway',
        503 => 'ServiceUnavailable',
        504 => 'GatewayTimeout',
        505 => 'HttpVersionNotSupported',
        506 => 'VariantAlsoNegotiates',
        507 => 'InsufficientStorage',
        509 => 'BandwidthLimitExceeded',
        510 => 'NotExtended'
    );

    /**
     * An HTTP status code
     *
     * @var integer
     */
    public $result_code;

    /**
     * A textual representation of the HTTP status code
     *
     * @var string
     */
    public $result_message;

    /**
     * An array containing additional properties of the result
     *
     * @var string[]
     */
    public $properties = array();

    /**
     *
     * @var bool
     */
    protected $returnActualStatusCode = false;

    /**
     * JsonAjaxResult Constructor
     *
     * @param integer $resultCode
     * @param string[] $properties
     */
    public function __construct($resultCode = 200, $properties = array())
    {
        $this->set_result_code($resultCode);
        $this->set_properties($properties);
    }

    /**
     *
     * @param string $resultMessage
     */
    public static function bad_request($resultMessage = null)
    {
        self::error(400, $resultMessage);
    }

    /**
     * Convert a JSON object string into a JsonAjaxResult This function only works with UTF-8 encoded data
     *
     * @param string $jsonString
     *
     * @return \Chamilo\Libraries\Architecture\JsonAjaxResult
     */
    public static function decode($jsonString)
    {
        $object = json_decode($jsonString);

        return new self();
    }

    public function display()
    {
        if ($this->returnActualStatusCode)
        {
            http_response_code($this->get_result_code());
        }
        header('Content-type: application/json');

        echo $this->encode();
        exit();
    }

    /**
     * Return a Json representation of the current object
     *
     * @return string
     */
    public function encode()
    {
        return json_encode($this);
    }

    /**
     *
     * @param integer $resultCode
     * @param strning $resultMessage
     */
    public static function error($resultCode = 404, $resultMessage = null)
    {
        $json_ajax_result = new self($resultCode);

        if ($resultMessage)
        {
            $json_ajax_result->set_result_message($resultMessage);
        }

        $json_ajax_result->display();
    }

    /**
     *
     * @param string $resultMessage
     */
    public static function general_error($resultMessage = null)
    {
        self::error(500, $resultMessage);
    }

    /**
     * Get all properties
     *
     * @return string[]
     */
    public function get_properties()
    {
        return $this->properties;
    }

    /**
     * Set all properties
     *
     * @param string[] $properties
     */
    public function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * Get a property
     *
     * @param string $property
     *
     * @return string
     */
    public function get_property($property)
    {
        return $this->properties[$property];
    }

    /**
     * Return the result code
     *
     * @return integer
     */
    public function get_result_code()
    {
        return $this->result_code;
    }

    /**
     * Set the result code (and per extension the result message) Data must be UTF-8 encoded
     *
     * @param integer $result_code
     */
    public function set_result_code($result_code)
    {
        $this->result_code = $result_code;
        $this->result_message = Translation::get(self::$result_codes[$result_code]);
    }

    /**
     * Return the result message
     *
     * @return string
     */
    public function get_result_message()
    {
        return $this->result_message;
    }

    /**
     * Set the result message
     *
     * @param string $resultMessage
     */
    public function set_result_message($resultMessage)
    {
        $this->result_message = $resultMessage;
    }

    /**
     *
     * @param string $resultMessage
     */
    public static function not_allowed($resultMessage = null)
    {
        self::error(403, $resultMessage);
    }

    /**
     *
     * @param string $resultMessage
     */
    public static function not_found($resultMessage = null)
    {
        self::error(404, $resultMessage);
    }

    /**
     * Reset the result message to the default as defined by the result code
     */
    public function reset_result_message()
    {
        $this->result_message = Translation::get(self::$result_codes[$this->get_result_code()]);
    }

    /**
     *  For backwards compatibility. Every response returns a 200 status code unless this function is called
     */
    public function returnActualStatusCode()
    {
        $this->returnActualStatusCode = true;
    }

    /**
     * Set a property Data must be UTF-8 encoded
     *
     * @param string $property
     * @param string|string[]|string[][] $value
     */
    public function set_property($property, $value)
    {
        $this->properties[$property] = $value;
    }

    /**
     *
     * @param string $result_message
     */
    public static function success($resultMessage = null)
    {
        self::error(200, $resultMessage);
    }
}
