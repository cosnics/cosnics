<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Protocol\Webservice\Rest\Client
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class RestAuthentication
{
    /**
     * @var \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestClient
     */
    private $client;

    const TYPE_DIGEST = 'digest';
    const TYPE_BASIC = 'basic';

    /**
     * RestAuthentication constructor.
     *
     * @param \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestClient $client
     */
    public function __construct(RestClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestClient $client
     * @param string $type
     *
     * @return \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestAuthentication
     */
    public static function factory(RestClient $client, $type)
    {
        $mode = $client->get_mode();
        $rest_authentication_class = __NAMESPACE__ . '\Authentication\\' .
             (string) StringUtilities::getInstance()->createString($mode)->upperCamelize() . '\\' .
             (string) StringUtilities::getInstance()->createString($type)->upperCamelize();
        return new $rest_authentication_class($client);
    }

    /**
     * @return \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestClient
     */
    public function get_client()
    {
        return $this->client;
    }

    abstract public function authenticate();
}
