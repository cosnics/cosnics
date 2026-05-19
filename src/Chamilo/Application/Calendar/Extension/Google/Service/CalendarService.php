<?php
namespace Chamilo\Application\Calendar\Extension\Google\Service;

use Chamilo\Application\Calendar\Extension\Google\Architecture\Domain\CalendarProperties;
use Chamilo\Application\Calendar\Extension\Google\Architecture\Domain\EventIterator;
use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Core\User\Storage\Entity\User;
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

    public function __construct(
        protected CalendarRepository $calendarRepository, protected ConfigurablePathBuilder $configurablePathBuilder,
        protected EventsCacheService $eventsCacheService,
        protected OwnedCalendarsCacheService $ownedCalendarsCacheService
    )
    {
    }

    protected function getCalendarProperties(string $summary, string $description, string $timeZone): CalendarProperties
    {
        return new CalendarProperties($summary, $description, $timeZone);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function getEventsForCalendarIdentifierAndBetweenDates(
        User $user, string $calendarIdentifier, ?int $fromDate = null, ?int $toDate = null
    ): EventIterator
    {
        $googleCalendarEvents = $this->eventsCacheService->getEventsForCalendarIdentifierAndBetweenDates(
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
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function getOwnedCalendars(User $user): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        return $this->ownedCalendarsCacheService->getOwnedCalendars($user);
    }

    public function isAuthenticated(User $user): bool
    {
        return $this->calendarRepository->hasAccessToken($user);
    }

    public function isConfigured(): bool
    {
        return $this->calendarRepository->isConfigured();
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     */
    public function login(User $user, $authenticationCode = null): void
    {
        $this->calendarRepository->login($user, $authenticationCode);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\EntityAlreadyExistsException
     * @throws \Google_Auth_Exception
     */
    public function logout(User $user): void
    {
        $this->calendarRepository->logout($user);
    }
}