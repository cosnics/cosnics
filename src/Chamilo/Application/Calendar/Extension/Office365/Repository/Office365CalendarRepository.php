<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Repository;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Application\Calendar\Extension\Office365\Manager;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Office365\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Office365CalendarRepository
{
    const AUTHENTICATION_BASE_URL = 'https://login.microsoftonline.com/common/oauth2/';
    const CALENDAR_BASE_URL = 'https://outlook.office365.com/api/v1.0';

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
     * @var \Chamilo\Application\Calendar\Extension\Office365\Repository\Office365CalendarRepository
     */
    private static $instance;

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Office365\Repository\Office365CalendarRepository
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
            $token = unserialize(LocalSetting :: get('token', $configurationContext));

            self :: $instance = new static($clientId, $clientSecret, $tenantId, $token);
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
        $token = $this->getToken();
        return $token['access_token'];
    }

    /**
     *
     * @return string
     */
    public function getRefreshToken()
    {
        $token = $this->getToken();
        return $token['refresh_token'];
    }

    /**
     *
     * @return integer
     */
    public function getTokenExpirationTime()
    {
        $token = $this->getToken();
        return $token['expires_on'];
    }

    /**
     *
     * @param string $token
     * @return boolean
     */
    public function saveToken($token)
    {
        return LocalSetting :: create_local_setting(
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
        return ! empty($this->getAccessToken());
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

        if ($this->hasAccessToken() && $this->isAccessTokenExpired())
        {
            $token = $this->refreshToken();
            $this->saveToken($token);
        }

        return $this->office365Client;
    }

    private function refreshToken()
    {
        $client = $this->getGuzzleHttpClient();

        $request = $client->createRequest('POST', self :: AUTHENTICATION_BASE_URL . 'token');
        $postBody = $request->getBody();

        $replyUri = new Redirect($this->getReplyParameters());

        $postBody->setField('grant_type', 'refresh_token');
        $postBody->setField('client_id', $this->getClientId());
        $postBody->setField('scope', $this->getClientScope());
        $postBody->setField('refresh_token', $this->getRefreshToken());
        $postBody->setField('client_secret', $this->getClientSecret());

        try
        {
            $response = $client->send($request);
            var_dump($response);
            exit();
        }
        catch (\Exception $exception)
        {
            var_dump($exception);
            exit();
        }
    }

    /**
     *
     * @return boolean
     */
    private function isAccessTokenExpired()
    {
        return $this->getTokenExpirationTime() < time();
    }

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
            $replyUri = new Redirect($this->getReplyParameters());

            $redirect = new Redirect();
            $redirect->writeHeader($this->createAuthUrl($this->getClientId(), $replyUri));
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

    private function requestAccessToken($authenticationCode)
    {
        $client = $this->getGuzzleHttpClient();

        $request = $client->createRequest('POST', self :: AUTHENTICATION_BASE_URL . 'token');
        $postBody = $request->getBody();

        $replyUri = new Redirect($this->getReplyParameters());

        $postBody->setField('grant_type', 'authorization_code');
        $postBody->setField('client_id', $this->getClientId());
        $postBody->setField('scope', $this->getClientScope());
        $postBody->setField('code', $authenticationCode);
        $postBody->setField('client_secret', $this->getClientSecret());

        try
        {
            $response = $client->send($request);
            var_dump($response);
            exit();
        }
        catch (\Exception $exception)
        {
            var_dump($exception);
            exit();
        }
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

    private function createAuthUrl($clientId, Redirect $replyUri)
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
        $this->saveAccessToken(null);
    }

    /**
     *
     * @return \stdClass[]
     */
    public function findOwnedCalendars()
    {
        $availableCalendars = array();

        $samplePath = 'G:/calendarListSample.json';

        if (file_exists($samplePath))
        {
            $result = file_get_contents($samplePath);
            $result = json_decode($result);
            $calendarItems = $result->value;

            foreach ($calendarItems as $calendarItem)
            {
                $availableCalendar = new AvailableCalendar();

                $availableCalendar->setType(Manager :: package());
                $availableCalendar->setIdentifier($calendarItem->Id);
                $availableCalendar->setName($calendarItem->Name);

                $availableCalendars[] = $availableCalendar;
            }
        }

        return $availableCalendars;
    }

    /**
     *
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Chamilo\Libraries\Storage\ResultSet\ArrayResultSet
     */
    public function findEventsBetweenDates($fromDate, $toDate)
    {
        $samplePath = 'G:/calendarSample.json';

        if (file_exists($samplePath))
        {
            $result = file_get_contents($samplePath);
            $result = json_decode($result);
            $result = $result->value;
        }
        else
        {
            $result = array();
        }

        return new ArrayResultSet($result);
    }
}