<?php
namespace Chamilo\Application\Calendar\Extension\Google\Repository;

use Chamilo\Application\Calendar\Extension\Google\Architecture\Exception\NotConfiguredException;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarDataProvider;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use DateTime;
use DateTimeInterface;
use Exception;
use Google_Auth_Exception;
use Google_Cache_File;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Events;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Application\Calendar\Extension\Google\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRepository
{

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected ConfigurationConsulter $configurationConsulter;

    protected User $currentUser;

    protected UrlGenerator $urlGenerator;

    protected UserSettingService $userSettingService;

    private ?Google_Service_Calendar $calendarClient = null;

    private ?Google_Client $googleClient = null;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder, UrlGenerator $urlGenerator,
        ConfigurationConsulter $configurationConsulter, UserSettingService $userSettingService, User $currentUser
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->configurationConsulter = $configurationConsulter;
        $this->userSettingService = $userSettingService;
        $this->currentUser = $currentUser;
    }

    public function clearAccessToken(User $user): bool
    {
        return $this->getUserSettingService()->saveUserSettingForSettingContextVariableAndUser(
            Manager::CONTEXT, 'token', $user
        );
    }

    public function findEventsForCalendarIdentifierAndBetweenDates(
        User $user, string $calendarIdentifier, int $fromDate, int $toDate
    ): Google_Service_Calendar_Events
    {
        $timeMin = new DateTime();
        $timeMin->setTimestamp($fromDate);

        $timeMax = new DateTime();
        $timeMax->setTimestamp($toDate);

        try
        {
            return $this->getCalendarClient($user)->events->listEvents(
                $calendarIdentifier, [
                    'timeMin' => $timeMin->format(DateTimeInterface::RFC3339),
                    'timeMax' => $timeMax->format(
                        DateTimeInterface::RFC3339
                    )
                ]
            );
        }
        catch (NotConfiguredException)
        {
            return new Google_Service_Calendar_Events();
        }
        catch (Exception)
        {
            $this->clearAccessToken($user);

            return new Google_Service_Calendar_Events();
        }
    }

    /**
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     */
    public function findOwnedCalendars(User $user): array
    {
        try
        {
            $calendarItems =
                $this->getCalendarClient($user)->calendarList->listCalendarList(['minAccessRole' => 'owner'])->getItems(
                );

            $availableCalendars = [];

            foreach ($calendarItems as $calendarItem)
            {
                $availableCalendar = new AvailableCalendar();

                $availableCalendar->setType(CalendarDataProvider::CONTEXT);
                $availableCalendar->setIdentifier($calendarItem->id);
                $availableCalendar->setName($calendarItem->summary);
                $availableCalendar->setDescription($calendarItem->description);

                $availableCalendars[] = $availableCalendar;
            }

            return $availableCalendars;
        }
        catch (NotConfiguredException)
        {
            return [];
        }
    }

    public function getAccessToken(User $user): ?string
    {
        return $this->getUserSettingService()->getSettingForUser($user, Manager::CONTEXT, 'token');
    }

    public function getCacheIdentifier($userToken, $method, $additionalIdentifiers = []): string
    {
        $identifiers = [];

        $identifiers[] = $userToken;
        $identifiers[] = $method;
        $identifiers[] = $additionalIdentifiers;

        return md5(serialize($identifiers));
    }

    /**
     * @throws \Chamilo\Application\Calendar\Extension\Google\Architecture\Exception\NotConfiguredException
     */
    public function getCalendarClient(User $user): Google_Service_Calendar
    {
        if (!isset($this->calendarClient))
        {
            $this->calendarClient = new Google_Service_Calendar($this->getGoogleClient($user));
        }

        return $this->calendarClient;
    }

    /**
     * @throws \Chamilo\Application\Calendar\Extension\Google\Architecture\Exception\NotConfiguredException
     */
    public function getClientId(): string
    {
        return $this->getSetting('client_id');
    }

    /**
     * @throws \Chamilo\Application\Calendar\Extension\Google\Architecture\Exception\NotConfiguredException
     */
    public function getClientSecret(): string
    {
        return $this->getSetting('client_secret');
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @throws \Chamilo\Application\Calendar\Extension\Google\Architecture\Exception\NotConfiguredException
     */
    public function getDeveloperKey(): string
    {
        return $this->getSetting('developer_key');
    }

    /**
     * @throws \Chamilo\Application\Calendar\Extension\Google\Architecture\Exception\NotConfiguredException
     */
    public function getGoogleClient(User $user): Google_Client
    {
        if (!isset($this->googleClient))
        {
            $this->googleClient = new Google_Client();
            $this->googleClient->setDeveloperKey($this->getDeveloperKey());

            $this->googleClient->setClientId($this->getClientId());
            $this->googleClient->setClientSecret($this->getClientSecret());
            $this->googleClient->setScopes(['https://www.googleapis.com/auth/calendar.readonly']);
            $this->googleClient->setAccessType('offline');
            $this->googleClient->setApprovalPrompt('force');

            if ($this->hasAccessToken($user))
            {
                $this->googleClient->setAccessToken($this->getAccessToken($user));
            }

            $this->googleClient->setClassConfig(
                'Google_Cache_File', ['directory' => $this->getConfigurablePathBuilder()->getCachePath(__NAMESPACE__)]
            );

            $this->googleClient->setCache(new Google_Cache_File($this->googleClient));
        }

        if ($this->hasAccessToken($user) && $this->googleClient->isAccessTokenExpired())
        {
            try
            {
                $refreshToken = $this->googleClient->getRefreshToken();
                $this->googleClient->refreshToken($refreshToken);

                $this->saveAccessToken($user, $this->googleClient->getAccessToken());
            }
            catch (Exception)
            {
                $this->clearAccessToken($user);
            }
        }

        return $this->googleClient;
    }

    /**
     * @throws \Chamilo\Application\Calendar\Extension\Google\Architecture\Exception\NotConfiguredException
     */
    protected function getSetting(string $name): string
    {
        $value = $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, $name]);

        if (!$value)
        {
            throw new NotConfiguredException($name);
        }

        return $value;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    protected function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }

    public function hasAccessToken(User $user): bool
    {
        $accessToken = $this->getAccessToken($user);

        return !empty($accessToken);
    }

    public function isConfigured(): bool
    {
        try
        {
            return $this->getDeveloperKey() && $this->getClientId() && $this->getClientSecret();
        }
        catch (NotConfiguredException)
        {
            return false;
        }
    }

    public function login(User $user, $authenticationCode = null)
    {
        try
        {
            if ($this->hasAccessToken($user))
            {
                return true;
            }

            $googleClient = $this->getGoogleClient($user);

            $redirectUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_LOGIN
                ]
            );

            $googleClient->setRedirectUri($redirectUrl);

            if (isset($authenticationCode))
            {
                try
                {
                    $googleClient->authenticate($authenticationCode);

                    return $this->saveAccessToken($user, $googleClient->getAccessToken());
                }
                catch (Exception)
                {
                    return $this->clearAccessToken($user);
                }
            }
            else
            {
                $response = new RedirectResponse($googleClient->createAuthUrl());
                $response->send();
                exit;
            }
        }
        catch (NotConfiguredException)
        {
            return false;
        }
    }

    /**
     */
    public function logout(User $user): bool
    {
        try
        {
            if ($this->getGoogleClient($user)->revokeToken())
            {
                return $this->clearAccessToken($user);
            }

            return false;
        }
        catch (NotConfiguredException|Google_Auth_Exception)
        {
            return false;
        }
    }

    public function saveAccessToken(User $user, string $accessToken): bool
    {
        try
        {
            return $this->getUserSettingService()->saveUserSettingForSettingContextVariableAndUser(
                Manager::CONTEXT, 'token', $user, $accessToken
            );
        }
        catch (Exception)
        {
            return false;
        }
    }
}