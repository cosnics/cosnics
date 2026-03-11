<?php
namespace Chamilo\Application\Calendar\Extension\Google\Repository;

use Chamilo\Application\Calendar\Architecture\Domain\AvailableCalendar;
use Chamilo\Application\Calendar\Extension\Google\Architecture\Enum\ActionEnum;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
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
    protected ?string $clientId;

    protected ?string $clientSecret;

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected ?User $currentUser;

    protected ?string $developerKey;

    protected UrlGenerator $urlGenerator;

    protected UserService $userService;

    private ?Google_Service_Calendar $calendarClient = null;

    private ?Google_Client $googleClient = null;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder, UrlGenerator $urlGenerator, UserService $userService,
        ?User $currentUser = null, ?string $clientId = null, ?string $clientSecret = null, ?string $developerKey = null
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->userService = $userService;
        $this->currentUser = $currentUser;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->developerKey = $developerKey;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function clearAccessToken(User $user): bool
    {
        return $this->getUserService()->updateUserSetting($user, 'cosnics.libraries.protocol.google.token', '');
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findEventsForCalendarIdentifierAndBetweenDates(
        User $user, string $calendarIdentifier, int $fromDate, int $toDate
    ): Google_Service_Calendar_Events
    {
        try {
            $timeMin = new DateTime('@' . $fromDate);
            $timeMax = new DateTime('@' . $toDate);

            return $this->getCalendarClient($user)->events->listEvents(
                $calendarIdentifier, [
                    'timeMin' => $timeMin->format(DateTimeInterface::RFC3339),
                    'timeMax' => $timeMax->format(
                        DateTimeInterface::RFC3339
                    )
                ]
            );
        }
        catch (Exception) {
            $this->clearAccessToken($user);

            return new Google_Service_Calendar_Events();
        }
    }

    /**
     * @return \Chamilo\Application\Calendar\Architecture\Domain\AvailableCalendar[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findOwnedCalendars(User $user): array
    {
        $calendarItems =
            $this->getCalendarClient($user)->calendarList->listCalendarList(['minAccessRole' => 'owner'])->getItems();

        $availableCalendars = [];

        foreach ($calendarItems as $calendarItem) {
            $availableCalendar = new AvailableCalendar();

            $availableCalendar->setType(Manager::CONTEXT);
            $availableCalendar->setIdentifier($calendarItem->id);
            $availableCalendar->setName($calendarItem->summary);
            $availableCalendar->setDescription($calendarItem->description);

            $availableCalendars[] = $availableCalendar;
        }

        return $availableCalendars;
    }

    public function getAccessToken(User $user): ?string
    {
        return $this->getUserService()->findUserSetting($user, 'cosnics.libraries.protocol.google.token');
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getCalendarClient(User $user): Google_Service_Calendar
    {
        if (!isset($this->calendarClient)) {
            $this->calendarClient = new Google_Service_Calendar($this->getGoogleClient($user));
        }

        return $this->calendarClient;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getDeveloperKey(): ?string
    {
        return $this->developerKey;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getGoogleClient(User $user): Google_Client
    {
        if (!isset($this->googleClient)) {
            $this->googleClient = new Google_Client();
            $this->googleClient->setDeveloperKey($this->getDeveloperKey());

            $this->googleClient->setClientId($this->getClientId());
            $this->googleClient->setClientSecret($this->getClientSecret());
            $this->googleClient->setScopes(['https://www.googleapis.com/auth/calendar.readonly']);
            $this->googleClient->setAccessType('offline');
            $this->googleClient->setApprovalPrompt('force');

            if ($this->hasAccessToken($user)) {
                $this->googleClient->setAccessToken($this->getAccessToken($user));
            }

            /** @noinspection PhpParamsInspection */
            $this->googleClient->setClassConfig(
                'Google_Cache_File', ['directory' => $this->getConfigurablePathBuilder()->getCachePath(__NAMESPACE__)]
            );

            $this->googleClient->setCache(new Google_Cache_File($this->googleClient));
        }

        if ($this->hasAccessToken($user) && $this->googleClient->isAccessTokenExpired()) {
            try {
                $refreshToken = $this->googleClient->getRefreshToken();
                $this->googleClient->refreshToken($refreshToken);

                $this->saveAccessToken($user, $this->googleClient->getAccessToken());
            }
            catch (Exception) {
                $this->clearAccessToken($user);
            }
        }

        return $this->googleClient;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    protected function getUserService(): UserService
    {
        return $this->userService;
    }

    public function hasAccessToken(User $user): bool
    {
        $accessToken = $this->getAccessToken($user);

        return !empty($accessToken);
    }

    public function isConfigured(): bool
    {
        return $this->getDeveloperKey() && $this->getClientId() && $this->getClientSecret();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function login(User $user, $authenticationCode = null)
    {
        if ($this->hasAccessToken($user)) {
            return true;
        }

        $googleClient = $this->getGoogleClient($user);

        $redirectUrl = $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::LOGIN->value
            ]
        );

        $googleClient->setRedirectUri($redirectUrl);

        if (isset($authenticationCode)) {
            try {
                $googleClient->authenticate($authenticationCode);

                return $this->saveAccessToken($user, $googleClient->getAccessToken());
            }
            catch (Exception) {
                return $this->clearAccessToken($user);
            }
        }
        else {
            $response = new RedirectResponse($googleClient->createAuthUrl());
            $response->send();
            exit;
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Google_Auth_Exception
     */
    public function logout(User $user): bool
    {
        if ($this->getGoogleClient($user)->revokeToken()) {
            return $this->clearAccessToken($user);
        }

        return false;
    }

    public function saveAccessToken(User $user, string $accessToken): bool
    {
        try {
            return $this->getUserService()->updateUserSetting(
                $user, 'cosnics.libraries.protocol.google.token', $accessToken
            );
        }
        catch (Exception) {
            return false;
        }
    }
}