<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Repository;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Office365\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Office365CalendarRepository
{

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
    private $accessToken;

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
     * @param string $accessToken
     */
    public function __construct($developerKey, $clientId, $clientSecret, $tenantId, $tenantName, $accessToken = null)
    {
        $this->developerKey = $developerKey;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tenantId = $tenantId;
        $this->tenantName = $tenantName;
        $this->accessToken = $accessToken;
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

            $developerKey = $configuration->get_setting(array($configurationContext, 'developer_key'));
            $clientId = $configuration->get_setting(array($configurationContext, 'client_id'));
            $clientSecret = $configuration->get_setting(array($configurationContext, 'client_secret'));
            $tenantId = $configuration->get_setting(array($configurationContext, 'tenant_id'));
            $tenantName = $configuration->get_setting(array($configurationContext, 'tenant_name'));
            $accessToken = LocalSetting :: get('token', $configurationContext);

            self :: $instance = new static($developerKey, $clientId, $clientSecret, $tenantId, $accessToken);
        }

        return static :: $instance;
    }

    /**
     *
     * @return string
     */
    public function getDeveloperKey()
    {
        return $this->developerKey;
    }

    /**
     *
     * @param string $developerKey
     */
    public function setDeveloperKey($developerKey)
    {
        $this->developerKey = $developerKey;
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
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     *
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     *
     * @param string $accessToken
     * @return boolean
     */
    public function saveAccessToken($accessToken)
    {
        return LocalSetting :: create_local_setting(
            'token',
            $accessToken,
            \Chamilo\Application\Calendar\Extension\Google\Manager :: context());
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
     * @return \Office365_Client
     */
    public function getOffice365Client()
    {
        // Calendar.Read
        if (! isset($this->office365Client))
        {
            $this->office365Client = new \GuzzleHttp\Client();
            // $this->office365Client->setDeveloperKey($this->getDeveloperKey());

            // $this->office365Client->setClientId($this->getClientId());
            // $this->office365Client->setClientSecret($this->getClientSecret());
            // $this->office365Client->setScopes('https://www.office365apis.com/auth/calendar.readonly');
            // $this->office365Client->setAccessType('offline');

            // if ($this->hasAccessToken())
            // {
            // $this->office365Client->setAccessToken($this->getAccessToken());
            // }
        }

        // if ($this->hasAccessToken() && $this->office365Client->isAccessTokenExpired())
        // {
        // $refreshToken = $this->office365Client->getRefreshToken();
        // $this->office365Client->refreshToken($refreshToken);

        // LocalSetting :: create_local_setting(
        // 'token',
        // $this->getAccessToken(),
        // \Chamilo\Application\Calendar\Extension\Office365\Manager :: context());
        // }

        return $this->office365Client;
    }

    public function login($authenticationCode = null)
    {
        if ($this->hasAccessToken())
        {
            return true;
        }

        $office365Client = $this->getOffice365Client();

        if (isset($authenticationCode))
        {
            throw new \Exception('Office 365 authentication not implemented yet.');
            exit;
            $office365Client->authenticate($authenticationCode);
            return $this->saveAccessToken($office365Client->getAccessToken());
        }
        else
        {
            $replyUri = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Extension\Office365\Manager :: context(),
                    \Chamilo\Application\Calendar\Extension\Office365\Manager :: PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Office365\Manager :: ACTION_LOGIN));

            $redirect = new Redirect();
            $redirect->writeHeader($this->createAuthUrl($this->getClientId(), $replyUri));
        }
    }

    private function createAuthUrl($clientId, Redirect $replyUri)
    {
        $params = array(
            'response_type' => 'code',
            'redirect_uri' => $replyUri->getUrl(),
            'client_id' => $this->getClientId());

        return 'https://login.microsoftonline.com/' . $this->getTenantId() . '/oauth2/authorize' . "?" .
             http_build_query($params, '', '&');
    }

    public function logout()
    {
        // if ($this->getOffice365Client()->revokeToken())
        // {
        // $this->saveAccessToken(null);
        // }
    }

    /**
     *
     * @return \Office365_Service_Calendar
     */
    public function getCalendarClient()
    {
        // if (! isset($this->calendarClient))
        // {
        // $this->calendarClient = new \Office365_Service_Calendar($this->getOffice365Client());
        // }

        // return $this->calendarClient;
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
            $result = file_get_contents('G:/calendarSample.json');
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