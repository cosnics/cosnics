<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Architecture\Domain\CalendarProperties;
use Chamilo\Application\Calendar\Extension\Google\Architecture\Domain\EventIterator;
use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarService
{
    public const string PARAM_AUTHORIZATION_CODE = 'code';

    private CalendarRepository $calendarRepository;

    private ConfigurablePathBuilder $configurablePathBuilder;

    private EventsCacheService $eventsCacheService;

    private OwnedCalendarsCacheService $ownedCalendarsCacheService;

    public function __construct(
        CalendarRepository $calendarRepository, ConfigurablePathBuilder $configurablePathBuilder,
        EventsCacheService $eventsCacheService, OwnedCalendarsCacheService $ownedCalendarsCacheService
    )
    {
        $this->calendarRepository = $calendarRepository;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->eventsCacheService = $eventsCacheService;
        $this->ownedCalendarsCacheService = $ownedCalendarsCacheService;
    }

    private function getCalendarProperties(string $summary, string $description, string $timeZone): CalendarProperties
    {
        return new CalendarProperties($summary, $description, $timeZone);
    }

    public function getCalendarRepository(): CalendarRepository
    {
        return $this->calendarRepository;
    }

    protected function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getEventsCacheService(): EventsCacheService
    {
        return $this->eventsCacheService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getEventsForCalendarIdentifierAndBetweenDates(
        User $user, string $calendarIdentifier, ?int $fromDate = null, ?int $toDate = null
    ): EventIterator
    {
        $googleCalendarEvents = $this->getEventsCacheService()->getEventsForCalendarIdentifierAndBetweenDates(
            $user, $calendarIdentifier, $fromDate, $toDate
        );

        return new EventIterator(
            $this->getCalendarProperties(
                $googleCalendarEvents->getSummary(), $googleCalendarEvents->getDescription(),
                $googleCalendarEvents->getTimeZone()
            ), $googleCalendarEvents->getItems()
        );
    }

    /**
     *
     * @return \Chamilo\Application\Calendar\Architecture\Domain\AvailableCalendar[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getOwnedCalendars(User $user): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        return $this->getOwnedCalendarsCacheService()->getOwnedCalendars($user);
    }

    public function getOwnedCalendarsCacheService(): OwnedCalendarsCacheService
    {
        return $this->ownedCalendarsCacheService;
    }

    public function isAuthenticated(User $user): bool
    {
        return $this->getCalendarRepository()->hasAccessToken($user);
    }

    public function isConfigured(): bool
    {
        return $this->getCalendarRepository()->isConfigured();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function login(User $user, $authenticationCode = null): bool
    {
        return $this->getCalendarRepository()->login($user, $authenticationCode);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Google_Auth_Exception
     */
    public function logout(User $user): bool
    {
        return $this->getCalendarRepository()->logout($user);
    }
}