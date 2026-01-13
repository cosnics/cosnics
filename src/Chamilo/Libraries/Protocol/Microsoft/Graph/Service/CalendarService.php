<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UserNotFoundException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository;
use Microsoft\Graph\Generated\Models\Calendar;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarService
{

    protected CalendarRepository $calendarRepository;

    protected UserService $userService;

    public function __construct(UserService $userService, CalendarRepository $calendarRepository)
    {
        $this->userService = $userService;
        $this->calendarRepository = $calendarRepository;
    }

    /**
     * @return \Microsoft\Graph\Generated\Models\Event[]
     */
    public function findEventsForCalendarIdentifierAndBetweenDates(
        User $user, string $calendarIdentifier, int $fromDate, int $toDate
    ): array
    {
        try
        {
            return $this->getCalendarRepository()->findEventsForCalendarIdentifierAndBetweenDates(
                $this->getUserIdentifier($user), $calendarIdentifier, $fromDate, $toDate
            );
        }
        catch (UserNotFoundException)
        {
            return [];
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UserNotFoundException
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\CalendarNotFoundException
     */
    public function getCalendarByIdentifier(string $calendarIdentifier, User $user): Calendar
    {
        return $this->getCalendarRepository()->getCalendarByIdentifier(
            $this->getUserIdentifier($user), $calendarIdentifier
        );
    }

    protected function getCalendarRepository(): CalendarRepository
    {
        return $this->calendarRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\UserNotFoundException
     */
    protected function getUserIdentifier(User $user): ?string
    {
        return $this->getUserService()->getAndSaveUserIdentifier($user);
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService
     */
    protected function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @return \Microsoft\Graph\Generated\Models\Calendar[]
     */
    public function listOwnedCalendars(User $user): array
    {
        try
        {
            return $this->getCalendarRepository()->listOwnedCalendars($this->getUserIdentifier($user));
        }
        catch (UserNotFoundException)
        {
            return [];
        }
    }
}