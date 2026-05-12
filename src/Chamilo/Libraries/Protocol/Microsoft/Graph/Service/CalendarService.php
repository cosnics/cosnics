<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository;
use Microsoft\Graph\Generated\Models\Calendar;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarService
{
    public function __construct(protected UserService $userService, protected CalendarRepository $calendarRepository)
    {
    }

    /**
     * @return \Microsoft\Graph\Generated\Models\Event[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function findEventsForCalendarIdentifierAndBetweenDates(
        User $user, string $calendarIdentifier, int $fromDate, int $toDate
    ): array
    {
        try {
            return $this->calendarRepository->findEventsForCalendarIdentifierAndBetweenDates(
                $this->getUserIdentifier($user), $calendarIdentifier, $fromDate, $toDate
            );
        }
        catch (NoSuchUserException) {
            return [];
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchCalendarException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function getCalendarByIdentifier(string $calendarIdentifier, User $user): Calendar
    {
        return $this->calendarRepository->getCalendarByIdentifier(
            $this->getUserIdentifier($user), $calendarIdentifier
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Architecture\Exception\NoSuchUserException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getUserIdentifier(User $user): ?string
    {
        return $this->userService->getAndSaveUserIdentifier($user);
    }

    /**
     * @return \Microsoft\Graph\Generated\Models\Calendar[]
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function listOwnedCalendars(User $user): array
    {
        try {
            return $this->calendarRepository->listOwnedCalendars($this->getUserIdentifier($user));
        }
        catch (NoSuchUserException) {
            return [];
        }
    }
}