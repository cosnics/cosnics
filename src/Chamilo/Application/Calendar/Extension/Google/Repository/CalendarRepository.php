<?php

namespace Chamilo\Application\Calendar\Extension\Google\Repository;

use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use DateTime;
use DateTimeInterface;
use Exception;
use Google_Cache_File;
use Google_Client;
use Google_Service_Calendar;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRepository
{

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository
     */
    private static $instance;

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
     * Clears the access token for a user when the access token can no longer be refreshed or has become invalid
     *
     * @return bool
     */
    public function clearAccessToken()
    {
        return LocalSetting::getInstance()->create(
            'token', null, Manager::context()
        );
    }

    /**
     *
     * @param string $calendarIdentifier
     * @param integer $fromDate
     * @param integer $toDate
     *
     * @return \Google_Service_Calendar_Events
     */
    public function findEventsForCalendarIdentifierAndBetweenDates($calendarIdentifier, $fromDate, $toDate)
    {
        $timeMin = new DateTime();
        $timeMin->setTimestamp($fromDate);

        $timeMax = new DateTime();
        $timeMax->setTimestamp($toDate);

        try
        {
            return $this->getCalendarClient()->events->listEvents(
                $calendarIdentifier, array(
                    'timeMin' => $timeMin->format(DateTimeInterface::RFC3339),
                    'timeMax' => $timeMax->format(
                        DateTimeInterface::RFC3339
                    )
                )
            );
        }
        catch (Exception $ex)
        {
            $this->clearAccessToken();

            return null;
        }
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function findOwnedCalendars()
    {
        try
        {
            $calendarItems =
                $this->getCalendarClient()->calendarList->listCalendarList(array('minAccessRole' => 'owner'))->getItems(
                    );
        }
        catch (Exception $ex)
        {
            $this->clearAccessToken();

            return [];
        }

        $availableCalendars = [];

        foreach ($calendarItems as $calendarItem)
        {
            $availableCalendar = new AvailableCalendar();

            $availableCalendar->setType(Manager::package());
            $availableCalendar->setIdentifier($calendarItem->id);
            $availableCalendar->setName($calendarItem->summary);
            $availableCalendar->setDescription($calendarItem->description);

            $availableCalendars[] = $availableCalendar;
        }

        return $availableCalendars;
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

    public function getCacheIdentifier($userToken, $method, $additionalIdentifiers = [])
    {
        $identifiers = [];

        $identifiers[] = $userToken;
        $identifiers[] = $method;
        $identifiers[] = $additionalIdentifiers;

        return md5(serialize($identifiers));
    }

    /**
     *
     * @return \Google_Service_Calendar
     */
    public function getCalendarClient()
    {
        if (!isset($this->calendarClient))
        {
            $this->calendarClient = new Google_Service_Calendar($this->getGoogleClient());
        }

        return $this->calendarClient;
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
     * @return \Google_Client
     */
    public function getGoogleClient()
    {
        if (!isset($this->googleClient))
        {
            $this->googleClient = new Google_Client();
            $this->googleClient->setDeveloperKey($this->getDeveloperKey());

            $this->googleClient->setClientId($this->getClientId());
            $this->googleClient->setClientSecret($this->getClientSecret());
            $this->googleClient->setScopes('https://www.googleapis.com/auth/calendar.readonly');
            $this->googleClient->setAccessType('offline');
            $this->googleClient->setApprovalPrompt('force');

            if ($this->hasAccessToken())
            {
                $this->googleClient->setAccessToken($this->getAccessToken());
            }

            $this->googleClient->setClassConfig(
                'Google_Cache_File', array('directory' => Path::getInstance()->getCachePath(__NAMESPACE__))
            );

            $this->googleClient->setCache(new Google_Cache_File($this->googleClient));
        }

        if ($this->hasAccessToken() && $this->googleClient->isAccessTokenExpired())
        {
            try
            {
                $refreshToken = $this->googleClient->getRefreshToken();
                $this->googleClient->refreshToken($refreshToken);

                $this->saveAccessToken($this->googleClient->getAccessToken());
            }
            catch (Exception $ex)
            {
                $this->clearAccessToken();
            }
        }

        return $this->googleClient;
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository
     */
    static public function getInstance()
    {
        if (is_null(static::$instance))
        {
            $configuration = Configuration::getInstance();
            $configurationContext = Manager::context();

            $developerKey = $configuration->get_setting(array($configurationContext, 'developer_key'));
            $clientId = $configuration->get_setting(array($configurationContext, 'client_id'));
            $clientSecret = $configuration->get_setting(array($configurationContext, 'client_secret'));
            $accessToken = LocalSetting::getInstance()->get('token', $configurationContext);

            self::$instance = new static($developerKey, $clientId, $clientSecret, $accessToken);
        }

        return static::$instance;
    }

    /**
     *
     * @return boolean
     */
    public function hasAccessToken()
    {
        $accessToken = $this->getAccessToken();

        return !empty($accessToken);
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
                Application::PARAM_CONTEXT => Manager::context(),
                Manager::PARAM_ACTION => Manager::ACTION_LOGIN
            )
        );

        $googleClient->setRedirectUri($redirect->getUrl());

        if (isset($authenticationCode))
        {
            try
            {
                $googleClient->authenticate($authenticationCode);

                return $this->saveAccessToken($googleClient->getAccessToken());
            }
            catch (Exception $ex)
            {
                $this->clearAccessToken();
            }
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
            return $this->clearAccessToken();
        }

        return false;
    }

    /**
     *
     * @param string $accessToken
     *
     * @return boolean
     */
    public function saveAccessToken($accessToken)
    {
        return LocalSetting::getInstance()->create(
            'token', $accessToken, Manager::context()
        );
    }
}