<?php
namespace Chamilo\Application\Calendar\Extension\Google\Repository;

use Chamilo\Application\Calendar\Architecture\Domain\AvailableCalendar;
use Chamilo\Application\Calendar\Extension\Google\Architecture\Enum\ActionEnum;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Core\User\Service\UserSettingsService;
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
    private ?Google_Service_Calendar $calendarClient = null;

    private ?Google_Client $googleClient = null;

    public function __construct(
        protected ConfigurablePathBuilder $configurablePathBuilder, protected UrlGenerator $urlGenerator,
        protected UserSettingsService $userSettingsService, protected ?string $clientId = null,
        protected ?string $clientSecret = null, protected ?string $developerKey = null
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function clearAccessToken(User $user): void
    {
        $this->userSettingsService->updateUserSetting($user, 'cosnics.libraries.protocol.google.token', '');
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
            $availableCalendars[] = new AvailableCalendar(
                Manager::CONTEXT, $calendarItem->id, $calendarItem->summary, $calendarItem->description
            );
        }

        return $availableCalendars;
    }

    public function getAccessToken(User $user): ?string
    {
        return $this->userSettingsService->findUserSetting($user, 'cosnics.libraries.protocol.google.token');
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getGoogleClient(User $user): Google_Client
    {
        if (!isset($this->googleClient)) {
            $this->googleClient = new Google_Client();
            $this->googleClient->setDeveloperKey($this->developerKey);

            $this->googleClient->setClientId($this->clientId);
            $this->googleClient->setClientSecret($this->clientSecret);
            $this->googleClient->setScopes(['https://www.googleapis.com/auth/calendar.readonly']);
            $this->googleClient->setAccessType('offline');
            $this->googleClient->setApprovalPrompt('force');

            if ($this->hasAccessToken($user)) {
                $this->googleClient->setAccessToken($this->getAccessToken($user));
            }

            /** @noinspection PhpParamsInspection */
            $this->googleClient->setClassConfig(
                'Google_Cache_File', ['directory' => $this->configurablePathBuilder->getCachePath(__NAMESPACE__)]
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

    public function hasAccessToken(User $user): bool
    {
        $accessToken = $this->getAccessToken($user);

        return !empty($accessToken);
    }

    public function isConfigured(): bool
    {
        return $this->developerKey && $this->clientId && $this->clientSecret;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function login(User $user, $authenticationCode = null): void
    {
        if (!$this->hasAccessToken($user)) {
            $googleClient = $this->getGoogleClient($user);

            $redirectUrl = $this->urlGenerator->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::LOGIN->value
                ]
            );

            $googleClient->setRedirectUri($redirectUrl);

            if (isset($authenticationCode)) {
                try {
                    $googleClient->authenticate($authenticationCode);

                    $this->saveAccessToken($user, $googleClient->getAccessToken());
                }
                catch (Exception) {
                    $this->clearAccessToken($user);
                }
            }
            else {
                $response = new RedirectResponse($googleClient->createAuthUrl());
                $response->send();
                exit;
            }
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Google_Auth_Exception
     */
    public function logout(User $user): void
    {
        if ($this->getGoogleClient($user)->revokeToken()) {
            $this->clearAccessToken($user);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function saveAccessToken(User $user, string $accessToken): void
    {
        $this->userSettingsService->updateUserSetting(
            $user, 'cosnics.libraries.protocol.google.token', $accessToken
        );
    }
}