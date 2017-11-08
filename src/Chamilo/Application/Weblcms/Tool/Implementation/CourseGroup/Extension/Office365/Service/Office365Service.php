<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception\Office365UserNotExistsException;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Office365Service
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository
     */
    protected $office365Repository;

    /**
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    protected $localSetting;

    /**
     * Office365Service constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository $office365Repository
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSetting
     */
    public function __construct(Office365Repository $office365Repository, LocalSetting $localSetting)
    {
        $this->office365Repository = $office365Repository;
        $this->localSetting = $localSetting;
    }

    /**
     * Creates a group by a given name
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param string $groupName
     *
     * @return string
     *
     * @throws \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception\Office365UserNotExistsException
     */
    public function createGroupByName(User $owner, $groupName)
    {
        //TODO: Temporarily hardcode this to avoid new groups being created
        return 'e5dcbd72-8938-4ed2-9b31-fbac1b04ea3b';
        $office365UserIdentifier = $this->getOffice365UserIdentifier($owner);

        if (empty($office365UserIdentifier))
        {
            throw new Office365UserNotExistsException($owner);
        }

        $group = $this->office365Repository->createGroup($groupName);
        $this->office365Repository->subscribeMemberInGroup($group, $office365UserIdentifier);

        return $group->getId();
    }

    /**
     * Updates the name of a group
     *
     * @param string $groupId
     * @param string $groupName
     */
    public function updateGroupName($groupId, $groupName)
    {
        $this->office365Repository->updateGroup($groupId, $groupName);
    }

    /**
     * Adds a member to a group. Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception\Office365UserNotExistsException
     */
    public function addMemberToGroup($groupId, User $user)
    {
        if (!$this->isMemberOfGroup($groupId, $user))
        {
            $office365UserIdentifier = $this->getOffice365UserIdentifier($user);

            if (empty($office365UserIdentifier))
            {
                throw new Office365UserNotExistsException($user);
            }

            $this->office365Repository->subscribeMemberInGroup($groupId, $office365UserIdentifier);
        }
    }

    /**
     * Removes a member from a group. Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception\Office365UserNotExistsException
     */
    public function removeMemberFromGroup($groupId, User $user)
    {
        if ($this->isMemberOfGroup($groupId, $user))
        {
            $office365UserIdentifier = $this->getOffice365UserIdentifier($user);

            if (empty($office365UserIdentifier))
            {
                throw new Office365UserNotExistsException($user);
            }

            $this->office365Repository->removeMemberFromGroup($groupId, $office365UserIdentifier);
        }
    }

    /**
     * Returns whether or not the given user is subscribed to the given group
     *
     * @param int $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function isMemberOfGroup($groupId, User $user)
    {
        $office365UserIdentifier = $this->getOffice365UserIdentifier($user);
        if (empty($office365UserIdentifier))
        {
            return false;
        }

        $groupMember = $this->office365Repository->getGroupMember($groupId, $office365UserIdentifier);

        return $groupMember instanceof \Microsoft\Graph\Model\User;
    }

    /**
     * Returns a list of external user identifiers that are subscribed as member in an office365 group
     *
     * @param string $groupId
     *
     * @return string[]
     */
    public function getGroupMembers($groupId)
    {
        $userIdentifiers = [];

        $groupMembers = $this->office365Repository->listGroupMembers($groupId);
        foreach ($groupMembers as $groupMember)
        {
            $userIdentifiers[] = $groupMember->getId();
        }

        return $userIdentifiers;
    }

    /**
     * Removes all the members from a given group
     *
     * @param string $groupId
     */
    public function removeAllMembersFromGroup($groupId)
    {
        $groupMembers = $this->getGroupMembers($groupId);
        foreach ($groupMembers as $groupMember)
        {
            $this->office365Repository->removeMemberFromGroup($groupId, $groupMember);
        }
    }

    /**
     * Adds a owner to a group. Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception\Office365UserNotExistsException
     */
    public function addOwnerToGroup($groupId, User $user)
    {
        if (!$this->isOwnerOfGroup($groupId, $user))
        {
            $office365UserIdentifier = $this->getOffice365UserIdentifier($user);
            if (empty($office365UserIdentifier))
            {
                throw new Office365UserNotExistsException($user);
            }

            $this->office365Repository->subscribeOwnerInGroup($groupId, $office365UserIdentifier);
        }
    }

    /**
     * Removes a owner from a group. Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Exception\Office365UserNotExistsException
     */
    public function removeOwnerFromGroup($groupId, User $user)
    {
        if ($this->isOwnerOfGroup($groupId, $user))
        {
            $office365UserIdentifier = $this->getOffice365UserIdentifier($user);
            if (empty($office365UserIdentifier))
            {
                throw new Office365UserNotExistsException($user);
            }

            $this->office365Repository->removeOwnerFromGroup($groupId, $office365UserIdentifier);
        }
    }

    /**
     * Returns whether or not the given user is subscribed to the given group
     *
     * @param int $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function isOwnerOfGroup($groupId, User $user)
    {
        $office365UserIdentifier = $this->getOffice365UserIdentifier($user);
        if (empty($office365UserIdentifier))
        {
            return false;
        }

        $groupOwner = $this->office365Repository->getGroupOwner($groupId, $office365UserIdentifier);

        return $groupOwner instanceof \Microsoft\Graph\Model\User;
    }

    /**
     * Returns a list of external user identifiers that are subscribed as owner in an office365 group
     *
     * @param string $groupId
     *
     * @return string[]
     */
    public function getGroupOwners($groupId)
    {
        $userIdentifiers = [];

        $groupOwners = $this->office365Repository->listGroupOwners($groupId);
        foreach ($groupOwners as $groupOwner)
        {
            $userIdentifiers[] = $groupOwner->getId();
        }

        return $userIdentifiers;
    }

    /**
     * Removes all the owners from a given group
     *
     * @param string $groupId
     */
    public function removeAllOwnersFromGroup($groupId)
    {
        $groupOwners = $this->getGroupOwners($groupId);
        foreach ($groupOwners as $groupOwner)
        {
            $this->office365Repository->removeOwnerFromGroup($groupId, $groupOwner);
        }
    }

    /**
     * Returns a list of all the plan identifiers of a given group
     *
     * @param string $groupId
     *
     * @return string[]
     */
    public function getGroupPlanIds($groupId)
    {
        $groupPlanIds = [];

        foreach ($this->office365Repository->listGroupPlans($groupId) as $groupPlan)
        {
            $groupPlanIds[] = $groupPlan->getId();
        }

        return $groupPlanIds;
    }

    /**
     * Returns the first plan identifier of a given group
     *
     * @param string $groupId
     *
     * @return string
     */
    public function getDefaultGroupPlanId($groupId)
    {
        $groupPlans = $this->office365Repository->listGroupPlans($groupId);

        if (empty($groupPlans))
        {
            return null;
        }

        return $groupPlans[0]->getId();
    }

    /**
     * Returns the identifier in office365 for a given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function getOffice365UserIdentifier(User $user)
    {
        $office365UserIdentifier = $this->localSetting->get(
            'external_user_id', 'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', $user
        );

        if (empty($office365UserIdentifier))
        {
            $office365User = $this->office365Repository->getOffice365User($user);

            if ($office365User instanceof \Microsoft\Graph\Model\User)
            {
                $office365UserIdentifier = $office365User->getId();
            }

            $this->localSetting->create(
                'external_user_id', $office365UserIdentifier,
                'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', $user
            );
        }

        return $office365UserIdentifier;
    }

    /**
     * Authorizes a user by a given authorization code
     *
     * @param string $authorizationCode
     */
    public function authorizeUserByAuthorizationCode($authorizationCode)
    {
        $this->office365Repository->authorizeUserByAuthorizationCode($authorizationCode);
    }

}