<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client;

use Chamilo\Libraries\Utilities\StringUtilities;

abstract class RestAuthentication
{

    private $client;
    const TYPE_DIGEST = 'digest';
    const TYPE_BASIC = 'basic';

    public function __construct(RestClient $client)
    {
        $this->client = $client;
    }

    public static function factory(RestClient $client, $type)
    {
        $mode = $client->get_mode();
        $rest_authentication_class = __NAMESPACE__ . '\Authentication\\' .
             (string) StringUtilities::getInstance()->createString($mode)->upperCamelize() . '\\' .
             (string) StringUtilities::getInstance()->createString($type)->upperCamelize();
        return new $rest_authentication_class($client);
    }

    public function get_client()
    {
        return $this->client;
    }

    abstract public function authenticate();
}
