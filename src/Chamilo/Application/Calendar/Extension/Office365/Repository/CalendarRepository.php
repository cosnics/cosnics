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
    const AUTHENTICATION_BASE_URL = 'https://login.microsoftonline.com/common/oauth2/';
    const CALENDAR_BASE_URL = 'https://outlook.office365.com/api/v1.0/';

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
    private $tenantId;

    /**
     *
     * @var string
     */
    private $tenantName;

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
     * @param string $tenantId
     * @param string $tenantName
     * @param string $token
     */
    public function __construct($clientId, $clientSecret, $tenantId, $tenantName, $token = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tenantId = $tenantId;
        $this->tenantName = $tenantName;
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
        if (is_null(static :: $instance))
        {
            $configuration = Configuration :: get_instance();
            $configurationContext = \Chamilo\Application\Calendar\Extension\Office365\Manager :: context();

            $clientId = $configuration->get_setting(array($configurationContext, 'client_id'));
            $clientSecret = $configuration->get_setting(array($configurationContext, 'client_secret'));
            $tenantId = $configuration->get_setting(array($configurationContext, 'tenant_id'));
            $tenantName = $configuration->get_setting(array($configurationContext, 'tenant_name'));
            $token = json_decode(LocalSetting :: getInstance()->get('token', $configurationContext));

            self :: $instance = new static($clientId, $clientSecret, $tenantId, $tenantName, $token);
        }

        return static :: $instance;
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
     * @return string
     */
    public function getTenantId()
    {
        return $this->tenantId;
    }

    /**
     *
     * @param string $tenantId
     */
    public function setTenantId($tenantId)
    {
        $this->tenantId = $tenantId;
    }

    /**
     *
     * @return string
     */
    public function getTenantName()
    {
        return $this->tenantName;
    }

    /**
     *
     * @param string $tenantName
     */
    public function setTenantName($tenantName)
    {
        $this->tenantName = $tenantName;
    }

    /**
     *
     * @return string
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
        return $this->getToken()->access_token;
    }

    /**
     *
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->getToken()->refresh_token;
    }

    /**
     *
     * @return integer
     */
    public function getTokenExpirationTime()
    {
        return $this->getToken()->expires_on;
    }

    /**
     *
     * @param string $token
     * @return boolean
     */
    public function saveToken($token)
    {
        $this->token = json_decode($token);
        return LocalSetting :: getInstance()->create(
            'token',
            $token,
            \Chamilo\Application\Calendar\Extension\Office365\Manager :: context());
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
            $this->office365Client = new \GuzzleHttp\Client(['base_url' => self :: CALENDAR_BASE_URL]);
        }

        return $this->office365Client;
    }

    /**
     *
     * @return \stdClass
     */
    private function refreshToken()
    {
        $client = $this->getGuzzleHttpClient();

        $request = $client->createRequest('POST', self :: AUTHENTICATION_BASE_URL . 'token');
        $postBody = $request->getBody();

        $replyUri = new Redirect();

        $postBody->setField('grant_type', 'refresh_token');
        $postBody->setField('client_id', $this->getClientId());
        $postBody->setField('scope', $this->getClientScope());
        $postBody->setField('redirect_uri', $replyUri->getUrl());
        $postBody->setField('resource', 'https://outlook.office365.com/');
        $postBody->setField('client_secret', $this->getClientSecret());
        $postBody->setField('refresh_token', $this->getRefreshToken());

        $response = $client->send($request);
        return $response->getBody()->getContents();
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
        if ($this->hasAccessToken())
        {
            return true;
        }

        $office365Client = $this->getGuzzleHttpClient();

        if (isset($authenticationCode))
        {
            $token = $this->requestAccessToken($authenticationCode);
            return $this->saveToken($token);
        }
        else
        {
            $replyUri = new Redirect();

            $redirect = new Redirect();
            $redirect->writeHeader($this->createAuthUrl($replyUri));
        }
    }

    /**
     *
     * @return string
     */
    private function getClientScope()
    {
        return 'https://outlook.office.com/Calendars.Read';
    }

    /**
     *
     * @param string $authenticationCode
     * @return \stdClass
     */
    private function requestAccessToken($authenticationCode)
    {
        $client = $this->getGuzzleHttpClient();
        $replyUri = new Redirect();

        $request = $client->createRequest('POST', self :: AUTHENTICATION_BASE_URL . 'token');
        $postBody = $request->getBody();

        $postBody->setField('grant_type', 'authorization_code');
        $postBody->setField('client_id', $this->getClientId());
        $postBody->setField('scope', $this->getClientScope());
        $postBody->setField('code', $authenticationCode);
        $postBody->setField('redirect_uri', $replyUri->getUrl());
        $postBody->setField('resource', 'https://outlook.office365.com/');
        $postBody->setField('client_secret', $this->getClientSecret());

        $response = $client->send($request);
        return $response->getBody()->getContents();
    }

    /**
     *
     * @return string[]
     */
    private function getReplyParameters()
    {
        return array(
            Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Extension\Office365\Manager :: context(),
            \Chamilo\Application\Calendar\Extension\Office365\Manager :: PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Office365\Manager :: ACTION_LOGIN);
    }

    /**
     *
     * @param Redirect $replyUri
     * @return string
     */
    private function createAuthUrl(Redirect $replyUri)
    {
        $params = array(
            'response_type' => 'code',
            'redirect_uri' => $replyUri->getUrl(),
            'state' => base64_encode(serialize($this->getReplyParameters())),
            'client_id' => $this->getClientId(),
            'response_mode' => 'query',
            'prompt' => 'login',
            'scope' => $this->getClientScope());

        return self :: AUTHENTICATION_BASE_URL . 'authorize' . "?" . http_build_query($params, '', '&');
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
        $request = $this->getGuzzleHttpClient()->createRequest('GET', 'me/calendars');
        $result = $this->executeRequest($request);

        $calendarItems = $result->value;

        $availableCalendars = array();

        foreach ($calendarItems as $calendarItem)
        {
            $availableCalendar = new AvailableCalendar();

            $availableCalendar->setType(Manager :: package());
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
        $request = $this->getGuzzleHttpClient()->createRequest('GET', 'me/calendars/' . $calendarIdentifier);
        $result = $this->executeRequest($request);

        $availableCalendar = new AvailableCalendar();

        $availableCalendar->setType(Manager :: package());
        $availableCalendar->setIdentifier($result->Id);
        $availableCalendar->setName($result->Name);

        return $availableCalendar;
    }

    /**
     *
     * @param \GuzzleHttp\Message\Request $request
     * @return \stdClass
     */
    private function executeRequest(\GuzzleHttp\Message\Request $request)
    {
        $lifetimeInMinutes = Configuration :: get_instance()->get_setting(
            array('Chamilo\Libraries\Calendar', 'refresh_external'));

        $parameterBag = new ParameterBag(
            array(
                ParameterBag :: PARAM_IDENTIFIER => md5(serialize($request)),
                RequestCacheService :: PARAM_REQUEST => $request,
                RequestCacheService :: PARAM_LIFETIME => $lifetimeInMinutes * 60));

        $cache = new RequestCacheService($this);
        return $cache->getForIdentifier($parameterBag);
    }

    /**
     *
     * @param \GuzzleHttp\Message\Request $request
     * @return \stdClass
     */
    public function sendRequest(\GuzzleHttp\Message\Request $request)
    {
        if ($this->hasAccessToken() && $this->isAccessTokenExpired())
        {
            $token = $this->refreshToken();
            $this->saveToken($token);
        }

        $client = $this->getGuzzleHttpClient();
        $request->addHeader('Authorization', 'Bearer ' . $this->getAccessToken());

        $response = $client->send($request);
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
        $request = $this->getGuzzleHttpClient()->createRequest(
            'GET',
            'me/calendars/' . $calendarIdentifier . '/calendarview',
            [
                'query' => [
                    '$top' => 200,
                    'startDateTime' => date('c', $fromDate),
                    'endDateTime' => date('c', $toDate)]]);

        $result = $this->executeRequest($request);

        return new ArrayResultSet($result->value);
    }
}