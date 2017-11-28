<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarService
{

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService
     */
    protected $userService;

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository
     */
    protected $calendarRepository;

    /**
     * CalendarService constructor
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService $userService
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository $calendarRepository
     */
    public function __construct(UserService $userService, CalendarRepository $calendarRepository)
    {
        $this->setUserService($userService);
        $this->setCalendarRepository($calendarRepository);
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService
     */
    protected function getUserService()
    {
        return $this->userService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService $userService
     */
    protected function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository
     */
    protected function getCalendarRepository()
    {
        return $this->calendarRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\CalendarRepository $calendarRepository
     */
    protected function setCalendarRepository(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     * Returns the identifier in azure active directory for a given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return string
     */
    protected function getAzureUserIdentifier(User $user)
    {
        return $this->getUserService()->getAzureUserIdentifier($user);
    }

    /**
     *
     * @param User $user
     *
     * @return \Microsoft\Graph\Model\Calendar[]
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function listOwnedCalendars(User $user)
    {
        $azureUserIdentifier = $this->getAzureUserIdentifier($user);

        if (empty($azureUserIdentifier))
        {
            throw new AzureUserNotExistsException($user);
        }

        return $this->getCalendarRepository()->listOwnedCalendars($azureUserIdentifier);
    }

    /**
     * @param string $calendarIdentifier
     * @param User $user
     *
     * @return \Microsoft\Graph\Model\Calendar
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function getCalendarByIdentifier($calendarIdentifier, User $user)
    {
        $azureUserIdentifier = $this->getAzureUserIdentifier($user);

        if (empty($azureUserIdentifier))
        {
            throw new AzureUserNotExistsException($user);
        }

        return $this->getCalendarRepository()->getCalendarByIdentifier($calendarIdentifier, $azureUserIdentifier);
    }

    /**
     * @param string $calendarIdentifier
     * @param User $user
     * @param integer $fromDate
     * @param integer $toDate
     *
     * @return \Microsoft\Graph\Model\Event[]
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function findEventsForCalendarIdentifierAndBetweenDates($calendarIdentifier, User $user, $fromDate, $toDate)
    {
        $azureUserIdentifier = $this->getAzureUserIdentifier($user);

        if (empty($azureUserIdentifier))
        {
            throw new AzureUserNotExistsException($user);
        }

        return $this->getCalendarRepository()->findEventsForCalendarIdentifierAndBetweenDates(
            $calendarIdentifier,
            $azureUserIdentifier,
            $fromDate,
            $toDate);
    }
}