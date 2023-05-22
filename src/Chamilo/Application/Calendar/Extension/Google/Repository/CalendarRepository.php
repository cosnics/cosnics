<?php
namespace Chamilo\Application\Calendar\Extension\Google\Repository;

use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use DateTime;
use DateTimeInterface;
use Exception;
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
    private static ?CalendarRepository $instance = null;

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected ConfigurationConsulter $configurationConsulter;

    protected User $currentUser;

    protected UrlGenerator $urlGenerator;

    protected UserService $userService;

    protected UserSettingService $userSettingService;

    private ?Google_Service_Calendar $calendarClient = null;

    private ?Google_Client $googleClient = null;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder, UrlGenerator $urlGenerator,
        ConfigurationConsulter $configurationConsulter, UserService $userService,
        UserSettingService $userSettingService, User $currentUser
    )
    {
        $this->userService = $userService;
        $this->urlGenerator = $urlGenerator;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->configurationConsulter = $configurationConsulter;
        $this->userSettingService = $userSettingService;
        $this->currentUser = $currentUser;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function clearAccessToken(): bool
    {
        return $this->getUserService()->createUserSettingForSettingAndUser(
            Manager::CONTEXT, 'token', $this->getCurrentUser()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findEventsForCalendarIdentifierAndBetweenDates(
        string $calendarIdentifier, int $fromDate, int $toDate
    ): Google_Service_Calendar_Events
    {
        $timeMin = new DateTime();
        $timeMin->setTimestamp($fromDate);

        $timeMax = new DateTime();
        $timeMax->setTimestamp($toDate);

        try
        {
            return $this->getCalendarClient()->events->listEvents(
                $calendarIdentifier, [
                    'timeMin' => $timeMin->format(DateTimeInterface::RFC3339),
                    'timeMax' => $timeMax->format(
                        DateTimeInterface::RFC3339
                    )
                ]
            );
        }
        catch (Exception $ex)
        {
            $this->clearAccessToken();

            return new Google_Service_Calendar_Events();
        }
    }

    /**
     * @return \Chamilo\Application\Calendar\Storage\DataClass\AvailableCalendar[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findOwnedCalendars(): array
    {
        try
        {
            $calendarItems =
                $this->getCalendarClient()->calendarList->listCalendarList(['minAccessRole' => 'owner'])->getItems();
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

            $availableCalendar->setType(Manager::CONTEXT);
            $availableCalendar->setIdentifier($calendarItem->id);
            $availableCalendar->setName($calendarItem->summary);
            $availableCalendar->setDescription($calendarItem->description);

            $availableCalendars[] = $availableCalendar;
        }

        return $availableCalendars;
    }

    public function getAccessToken(): string
    {
        return $this->getUserSettingService()->getSettingForUser($this->getCurrentUser(), Manager::CONTEXT, 'token');
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getCalendarClient(): Google_Service_Calendar
    {
        if (!isset($this->calendarClient))
        {
            $this->calendarClient = new Google_Service_Calendar($this->getGoogleClient());
        }

        return $this->calendarClient;
    }

    public function getClientId(): string
    {
        return $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'client_id']);
    }

    public function getClientSecret(): string
    {
        return $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'client_secret']);
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getCurrentUser(): User
    {
        return $this->currentUser;
    }

    public function getDeveloperKey(): string
    {
        return $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'developer_key']);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getGoogleClient(): Google_Client
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

            if ($this->hasAccessToken())
            {
                $this->googleClient->setAccessToken($this->getAccessToken());
            }

            $this->googleClient->setClassConfig(
                'Google_Cache_File', ['directory' => $this->getConfigurablePathBuilder()->getCachePath(__NAMESPACE__)]
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
     * @throws \Exception
     */
    public static function getInstance(): CalendarRepository
    {
        if (is_null(static::$instance))
        {
            $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

            self::$instance = new static(
                $container->get(SystemPathBuilder::class), $container->get(UrlGenerator::class),
                $container->get(ConfigurationConsulter::class), $container->get(UserService::class),
                $container->get(UserSetting::class), $container->get('Chamilo\Core\User\CurrentUser')
            );
        }

        return static::$instance;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    protected function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }

    public function hasAccessToken(): bool
    {
        $accessToken = $this->getAccessToken();

        return !empty($accessToken);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function login($authenticationCode = null)
    {
        if ($this->hasAccessToken())
        {
            return true;
        }

        $googleClient = $this->getGoogleClient();

        $redirectUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_ACTION => Manager::ACTION_LOGIN
            ]
        );

        $googleClient->setRedirectUri($redirectUrl);

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

                return false;
            }
        }
        else
        {
            $response = new RedirectResponse($googleClient->createAuthUrl());
            $response->send();
            exit;
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Google_Auth_Exception
     */
    public function logout(): bool
    {
        if ($this->getGoogleClient()->revokeToken())
        {
            return $this->clearAccessToken();
        }

        return false;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function saveAccessToken(string $accessToken): bool
    {
        return $this->getUserService()->createUserSettingForSettingAndUser(
            Manager::CONTEXT, 'token', $this->getCurrentUser(), $accessToken
        );
    }
}