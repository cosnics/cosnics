<?php
namespace Chamilo\Application\Calendar\Extension\Google\Repository;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Application\Calendar\Extension\Google\Manager;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class GoogleCalendarRepository
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
    private $accessToken;

    /**
     *
     * @var \Google_Client
     */
    private $googleClient;

    /**
     *
     * @var \Google_Service_Calendar
     */
    private $calendarClient;

    /**
     *
     * @param string $developerKey
     * @param string $clientId
     * @param string $clientSecret
     * @param string $accessToken
     */
    public function __construct($developerKey, $clientId, $clientSecret, $accessToken = null)
    {
        $this->developerKey = $developerKey;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = $accessToken;
    }

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository
     */
    private static $instance;

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository
     */
    static public function getInstance()
    {
        if (is_null(static :: $instance))
        {
            $configuration = Configuration :: get_instance();
            $configurationContext = \Chamilo\Application\Calendar\Extension\Google\Manager :: context();

            $developerKey = $configuration->get_setting(array($configurationContext, 'developer_key'));
            $clientId = $configuration->get_setting(array($configurationContext, 'client_id'));
            $clientSecret = $configuration->get_setting(array($configurationContext, 'client_secret'));
            $accessToken = LocalSetting :: get('token', $configurationContext);

            self :: $instance = new static($developerKey, $clientId, $clientSecret, $accessToken);
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
     * @return \Google_Client
     */
    public function getGoogleClient()
    {
        if (! isset($this->googleClient))
        {
            $this->googleClient = new \Google_Client();
            $this->googleClient->setDeveloperKey($this->getDeveloperKey());

            $this->googleClient->setClientId($this->getClientId());
            $this->googleClient->setClientSecret($this->getClientSecret());
            $this->googleClient->setScopes('https://www.googleapis.com/auth/calendar.readonly');
            $this->googleClient->setAccessType('offline');

            if ($this->hasAccessToken())
            {
                $this->googleClient->setAccessToken($this->getAccessToken());
            }
        }

        if ($this->hasAccessToken() && $this->googleClient->isAccessTokenExpired())
        {
            $refreshToken = $this->googleClient->getRefreshToken();
            $this->googleClient->refreshToken($refreshToken);

            $this->saveAccessToken($this->googleClient->getAccessToken());
        }

        return $this->googleClient;
    }

    public function login($authenticationCode = null)
    {
        if ($this->hasAccessToken())
        {
            return true;
        }

        $googleClient = $this->getGoogleClient();

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Extension\Google\Manager :: context(),
                \Chamilo\Application\Calendar\Extension\Google\Manager :: PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Google\Manager :: ACTION_LOGIN));

        $googleClient->setRedirectUri($redirect->getUrl());

        if (isset($authenticationCode))
        {
            $googleClient->authenticate($authenticationCode);
            return $this->saveAccessToken($googleClient->getAccessToken());
        }
        else
        {
            $redirect = new Redirect();
            $redirect->writeHeader($googleClient->createAuthUrl());
        }
    }

    public function logout()
    {
        if ($this->getGoogleClient()->revokeToken())
        {
            $this->saveAccessToken(null);
        }
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
     * @return \Google_Service_Calendar
     */
    public function getCalendarClient()
    {
        if (! isset($this->calendarClient))
        {
            $this->calendarClient = new \Google_Service_Calendar($this->getGoogleClient());
        }

        return $this->calendarClient;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function findOwnedCalendars()
    {
        $calendarItems = $this->getCalendarClient()->calendarList->listCalendarList(array('minAccessRole' => 'owner'))->getItems();

        $availableCalendars = array();

        foreach ($calendarItems as $calendarItem)
        {
            $availableCalendar = new AvailableCalendar();

            $availableCalendar->setType(Manager :: package());
            $availableCalendar->setIdentifier($calendarItem->id);
            $availableCalendar->setName($calendarItem->summary);
            $availableCalendar->setDescription($calendarItem->description);

            $availableCalendars[] = $availableCalendar;
        }

        return $availableCalendars;
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Google_Service_Calendar_Events
     */
    public function findEventsForCalendarIdentifierAndBetweenDates($calendarIdentifier, $fromDate, $toDate)
    {
        $timeMin = new \DateTime();
        $timeMin->setTimestamp($fromDate);

        $timeMax = new \DateTime();
        $timeMax->setTimestamp($toDate);

        return $this->getCalendarClient()->events->listEvents(
            $calendarIdentifier,
            array(
                'timeMin' => $timeMin->format(\DateTime :: RFC3339),
                'timeMax' => $timeMax->format(\DateTime :: RFC3339)));
    }
}