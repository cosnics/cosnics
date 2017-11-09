<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Repository;

use Chamilo\Application\Calendar\Extension\Office365\Manager;
use Chamilo\Application\Calendar\Extension\Office365\Service\RequestCacheService;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Cache\ParameterBag;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Office365\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRepository
{
    const CALENDAR_BASE_URL = 'https://outlook.office365.com/api/v2.0/';

    /**
     *
     * @var string
     */
    private $developerKey;

    /**
     *
     * @var string
     */
    private $clientId;

    /**
     *
     * @var string
     */
    private $clientSecret;

    /**
     *
     * @var string
     */
    private $token;

    /**
     *
     * @var \Office365_Client
     */
    private $office365Client;

    /**
     *
     * @var \Office365_Service_Calendar
     */
    private $calendarClient;

    /**
     *
     * @param string $developerKey
     * @param string $clientId
     * @param string $clientSecret
     * @param string $token
     */
    public function __construct($clientId, $clientSecret, $token = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->token = $token;
    }

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository
     */
    private static $instance;

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository
     */
    static public function getInstance()
    {
        if (is_null(static::$instance))
        {
            $configuration = Configuration::getInstance();
            $configurationContext = \Chamilo\Application\Calendar\Extension\Office365\Manager::context();

            $clientId = $configuration->get_setting(array($configurationContext, 'client_id'));
            $clientSecret = $configuration->get_setting(array($configurationContext, 'client_secret'));
            $token = unserialize(LocalSetting::getInstance()->get('token', $configurationContext));

            self::$instance = new static($clientId, $clientSecret, $token);
        }

        return static::$instance;
    }

    /**
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     *
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     *
     * @param string $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     *
     * @return \TheNetworg\OAuth2\Client\Token\AccessToken
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @return string
     */
    public function getAccessToken()
    {
        if ($this->getToken() instanceof \TheNetworg\OAuth2\Client\Token\AccessToken)
        {
            return $this->getToken()->getToken();
        }
    }

    /**
     *
     * @return string
     */
    public function getRefreshToken()
    {
        if ($this->getToken() instanceof \TheNetworg\OAuth2\Client\Token\AccessToken)
        {
            return $this->getToken()->getRefreshToken();
        }
    }

    /**
     *
     * @return integer
     */
    public function getTokenExpirationTime()
    {
        if ($this->getToken() instanceof \TheNetworg\OAuth2\Client\Token\AccessToken)
        {
            return $this->getToken()->getExpires();
        }
    }

    /**
     *
     * @param string $token
     * @return boolean
     */
    public function saveToken($token)
    {
        $this->token = $token;
        return LocalSetting::getInstance()->create(
            'token',
            $token,
            \Chamilo\Application\Calendar\Extension\Office365\Manager::context());
    }

    /**
     *
     * @return boolean
     */
    public function hasAccessToken()
    {
        $accessToken = $this->getAccessToken();
        return ! empty($accessToken);
    }

    /**
     *
     * @return \GuzzleHttp\Client
     */
    public function getGuzzleHttpClient()
    {
        if (! isset($this->office365Client))
        {
            $this->office365Client = new \GuzzleHttp\Client(['base_uri' => self::CALENDAR_BASE_URL]);
        }

        return $this->office365Client;
    }

    /**
     *
     * @return boolean
     */
    private function isAccessTokenExpired()
    {
        return $this->getTokenExpirationTime() < time();
    }

    /**
     *
     * @param unknown $authenticationCode
     * @return boolean
     */
    public function login($authenticationCode = null)
    {
        // if ($this->hasAccessToken())
        // {
        // return true;
        // }
        $provider = $this->getOAuthProvider();

        //         if (isset($authenticationCode))
            //         {
        $token = $provider->getAccessToken(
            'client_credentials',
            [
                'scope' => $this->getClientScope(),
                'resource' => ['https://graph.microsoft.com/']]);
            var_dump($token);
            echo $token->getToken();
            exit;
            return $this->saveToken(serialize($token));
            //         }
        //         else
            //         {
            //             $authUrl = $provider->getAuthorizationUrl(
            //                 ['state' => base64_encode(serialize($this->getReplyParameters()))]);
            //             $_SESSION['oauth2state'] = $provider->getState();
            //             header('Location: ' . $authUrl);
            //             exit();
            //         }
    }

    /**
     *
     * @return \TheNetworg\OAuth2\Client\Provider\Azure
     */
    protected function getOAuthProvider()
    {
        $replyUri = new Redirect();

        $provider = new \TheNetworg\OAuth2\Client\Provider\Azure(
            [
                'clientId' => $this->getClientId(),
                'clientSecret' => $this->getClientSecret(),
                'redirectUri' => $replyUri->getUrl()]);

            $provider->pathAuthorize = "/oauth2/v2.0/authorize";
            $provider->pathToken = "/oauth2/v2.0/token";
            $provider->scope = [$this->getClientScope()];
            $provider->resource = ['https://graph.microsoft.com/'];

            return $provider;
    }

    protected function refreshToken()
    {
        return $this->getOAuthProvider()->getAccessToken('refresh_token', [
            'refresh_token' => $this->getRefreshToken()]);
    }

    /**
     *
     * @return string
     */
    private function getClientScope()
    {
        return 'https://graph.microsoft.com/.default';
    }

    /**
     *
     * @return string[]
     */
    private function getReplyParameters()
    {
        return array(
            Application::PARAM_CONTEXT => \Chamilo\Application\Calendar\Extension\Office365\Manager::context(),
            \Chamilo\Application\Calendar\Extension\Office365\Manager::PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Office365\Manager::ACTION_LOGIN);
    }

    public function logout()
    {
        return $this->saveToken(null);
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function findOwnedCalendars()
    {
        $calendarItems = $this->executeRequest($this->buildRequest('GET', 'me/calendars'));

        $availableCalendars = array();

        foreach ($calendarItems->value as $calendarItem)
        {
            $availableCalendar = new AvailableCalendar();

            $availableCalendar->setType(Manager::package());
            $availableCalendar->setIdentifier($calendarItem->Id);
            $availableCalendar->setName($calendarItem->Name);

            $availableCalendars[] = $availableCalendar;
        }

        return $availableCalendars;
    }

    /**
     *
     * @param string $calendarIdentifier
     */
    public function findCalendarByIdentifier($calendarIdentifier)
    {
        $result = $this->executeRequest($this->buildRequest('GET', 'me/calendars/' . $calendarIdentifier));

        $availableCalendar = new AvailableCalendar();

        $availableCalendar->setType(Manager::package());
        $availableCalendar->setIdentifier($result->Id);
        $availableCalendar->setName($result->Name);

        return $availableCalendar;
    }

    public function buildRequest($type, $endpoint, $queryParameters = [])
    {
        if ($this->hasAccessToken() && $this->isAccessTokenExpired())
        {
            $this->saveToken($this->refreshToken());
        }

        $uri = self::CALENDAR_BASE_URL . $this->buildEndpoint($endpoint, $queryParameters);
        $headers = ['Authorization' => 'Bearer ' . $this->getAccessToken()];

        return new \GuzzleHttp\Psr7\Request($type, $uri, $headers);
    }

    /**
     *
     * @param string $endpoint
     * @param string[] $queryParameters
     * @return string
     */
    protected function buildEndpoint($endpoint, $queryParameters = array())
    {
        if (count($queryParameters) > 0)
        {
            $endpoint .= '?' . http_build_query($queryParameters);
        }

        $endpoint = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $endpoint);

        return $endpoint;
    }

    /**
     *
     * @param \Guzzle\Http\Message\Request $request
     * @return \stdClass
     */
    private function executeRequest(\GuzzleHttp\Psr7\Request $request)
    {
        $lifetimeInMinutes = Configuration::getInstance()->get_setting(
            array('Chamilo\Libraries\Calendar', 'refresh_external'));

        $parameterBag = new ParameterBag(
            array(
                ParameterBag::PARAM_IDENTIFIER => md5(serialize($request)),
                RequestCacheService::PARAM_REQUEST => $request,
                RequestCacheService::PARAM_LIFETIME => $lifetimeInMinutes * 60));

        $cache = new RequestCacheService($this);
        return $cache->getForIdentifier($parameterBag);
    }

    /**
     *
     * @param \GuzzleHttp\Psr7\Request $request
     * @return \stdClass
     */
    public function sendRequest(\GuzzleHttp\Psr7\Request $request)
    {
        $response = $this->getGuzzleHttpClient()->send($request);
        return json_decode($response->getBody()->getContents());
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Chamilo\Libraries\Storage\ResultSet\ArrayResultSet
     */
    public function findEventsForCalendarIdentifierAndBetweenDates($calendarIdentifier, $fromDate, $toDate)
    {
        $result = $this->executeRequest(
            $this->buildRequest(
                'GET',
                'me/calendars/' . $calendarIdentifier . '/calendarview',
                ['$top' => 200, 'startDateTime' => date('c', $fromDate), 'endDateTime' => date('c', $toDate)]));

        return new ArrayResultSet($result->value);
    }
}