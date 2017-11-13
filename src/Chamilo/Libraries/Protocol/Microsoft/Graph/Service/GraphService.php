<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GraphService
{

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService
     */
    private $userService;

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService
     */
    private $groupService;

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService
     */
    private $calendarService;

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService $userService
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService $groupService
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService $calendarService
     */
    public function __construct(UserService $userService, GroupService $groupService, CalendarService $calendarService)
    {
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->calendarService = $calendarService;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService
     */
    public function getGroupService()
    {
        return $this->groupService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService $groupService
     */
    public function setGroupService(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     *
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService
     */
    public function getCalendarService()
    {
        return $this->calendarService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService $calendarService
     */
    public function setCalendarService(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService::getAzureUserIdentifier()
     */
    public function getAzureUserIdentifier(User $user)
    {
        return $this->getUserService()->getAzureUserIdentifier($user);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService::authorizeUserByAuthorizationCode()
     */
    public function authorizeUserByAuthorizationCode($authorizationCode)
    {
        return $this->getUserService()->authorizeUserByAuthorizationCode($authorizationCode);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::createGroupByName()
     */
    public function createGroupByName(User $owner, $groupName)
    {
        return $this->getGroupService()->createGroupByName($owner, $groupName);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::updateGroupName()
     */
    public function updateGroupName($groupId, $groupName)
    {
        $this->getGroupService()->updateGroupName($groupId, $groupName);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::addMemberToGroup()
     */
    public function addMemberToGroup($groupId, User $user)
    {
        $this->getGroupService()->addMemberToGroup($groupId, $user);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::removeMemberFromGroup()
     */
    public function removeMemberFromGroup($groupId, User $user)
    {
        $this->getGroupService()->removeMemberFromGroup($groupId, $user);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::isMemberOfGroup()
     */
    public function isMemberOfGroup($groupId, User $user)
    {
        return $this->getGroupService()->isMemberOfGroup($groupId, $user);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::getGroupMembers()
     */
    public function getGroupMembers($groupId)
    {
        return $this->getGroupService()->getGroupMembers($groupId);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::removeAllMembersFromGroup()
     */
    public function removeAllMembersFromGroup($groupId)
    {
        $this->getGroupService()->removeAllMembersFromGroup($groupId);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::addOwnerToGroup()
     */
    public function addOwnerToGroup($groupId, User $user)
    {
        $this->getGroupService()->addOwnerToGroup($groupId, $user);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::removeOwnerFromGroup()
     */
    public function removeOwnerFromGroup($groupId, User $user)
    {
        $this->getGroupService()->removeOwnerFromGroup($groupId, $user);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::isOwnerOfGroup()
     */
    public function isOwnerOfGroup($groupId, User $user)
    {
        return $this->getGroupService()->isOwnerOfGroup($groupId, $user);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::getGroupOwners()
     */
    public function getGroupOwners($groupId)
    {
        return $this->getGroupService()->getGroupOwners($groupId);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::removeAllOwnersFromGroup()
     */
    public function removeAllOwnersFromGroup($groupId)
    {
        $this->getGroupService()->removeAllOwnersFromGroup($groupId);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::getGroupPlanIds()
     */
    public function getGroupPlanIds($groupId)
    {
        return $this->getGroupService()->getGroupPlanIds($groupId);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService::getDefaultGroupPlanId()
     */
    public function getDefaultGroupPlanId($groupId)
    {
        return $this->getGroupService()->getDefaultGroupPlanId($groupId);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService::listOwnedCalendars()
     */
    public function listOwnedCalendars(User $user)
    {
        return $this->getCalendarService()->listOwnedCalendars($user);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService::getCalendarByIdentifier()
     */
    public function getCalendarByIdentifier($calendarIdentifier, User $user)
    {
        return $this->getCalendarService()->getCalendarByIdentifier($calendarIdentifier, $user);
    }

    /**
     *
     * @see \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\CalendarService::findEventsForCalendarIdentifierAndBetweenDates()
     */
    public function findEventsForCalendarIdentifierAndBetweenDates($calendarIdentifier, User $user, $fromDate, $toDate)
    {
        return $this->getCalendarService()->findEventsForCalendarIdentifierAndBetweenDates(
            $calendarIdentifier,
            $user,
            $fromDate,
            $toDate);
    }
}