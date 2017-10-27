<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service;

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
     */
    public function createGroupByName(User $owner, $groupName)
    {
        $office365UserIdentifier = $this->getOffice365UserIdentifier($owner);

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
     */
    public function addMemberToGroup($groupId, User $user)
    {
        if (!$this->isMemberOfGroup($groupId, $user))
        {
            $office365UserIdentifier = $this->getOffice365UserIdentifier($user);

            $this->office365Repository->subscribeMemberInGroup($groupId, $office365UserIdentifier);
        }
    }

    /**
     * Removes a member from a group. Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function removeMemberFromGroup($groupId, User $user)
    {
        if ($this->isMemberOfGroup($groupId, $user))
        {
            $office365UserIdentifier = $this->getOffice365UserIdentifier($user);

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

        $groupMembers = $this->office365Repository->listMembers($groupId);
        foreach($groupMembers as $groupMember)
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
        foreach($groupMembers as $groupMember)
        {
            $this->office365Repository->removeMemberFromGroup($groupId, $groupMember);
        }
    }

    /**
     * Adds a owner to a group. Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function addOwnerToGroup($groupId, User $user)
    {
        if (!$this->isOwnerOfGroup($groupId, $user))
        {
            $office365UserIdentifier = $this->getOffice365UserIdentifier($user);

            $this->office365Repository->subscribeOwnerInGroup($groupId, $office365UserIdentifier);
        }
    }

    /**
     * Removes a owner from a group. Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function removeOwnerFromGroup($groupId, User $user)
    {
        if ($this->isOwnerOfGroup($groupId, $user))
        {
            $office365UserIdentifier = $this->getOffice365UserIdentifier($user);

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

        $groupOwners = $this->office365Repository->listOwners($groupId);
        foreach($groupOwners as $groupOwner)
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
        foreach($groupOwners as $groupOwner)
        {
            $this->office365Repository->removeOwnerFromGroup($groupId, $groupOwner);
        }
    }


    /**
     * Removes all the users from a given group
     *
     * @param string $groupId
     */
    public function removeAllUsersFromGroup($groupId)
    {
        $this->removeAllOwnersFromGroup($groupId);
        $this->removeAllMembersFromGroup($groupId);
    }

    /**
     * Returns the identifier in office365 for a given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    protected function getOffice365UserIdentifier(User $user)
    {
        $office365UserIdentifier = $this->localSetting->get(
            'external_user_id', 'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', $user
        );

        if (empty($office365UserIdentifier))
        {
            $office365User = $this->office365Repository->getOffice365User($user);
            $office365UserIdentifier = $office365User->getId();

            $this->localSetting->create(
                'external_user_id', $office365UserIdentifier,
                'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365', $user
            );
        }

        return $office365UserIdentifier;
    }
}