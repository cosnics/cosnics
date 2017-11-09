<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureActiveDirectoryUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GraphService
{

    /**
     *
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository
     */
    protected $graphRepository;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    protected $localSetting;

    /**
     * GraphService constructor.
     *
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Storage\Repository\GraphRepository $graphRepository
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSetting
     */
    public function __construct(GraphRepository $graphRepository, LocalSetting $localSetting)
    {
        $this->graphRepository = $graphRepository;
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
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureActiveDirectoryUserNotExistsException
     */
    public function createGroupByName(User $owner, $groupName)
    {
        // TODO: Temporarily hardcode this to avoid new groups being created
        return 'e5dcbd72-8938-4ed2-9b31-fbac1b04ea3b';
        $azureActiveDirectoryUserIdentifier = $this->getAzureActiveDirectoryUserIdentifier($owner);

        if (empty($azureActiveDirectoryUserIdentifier))
        {
            throw new AzureActiveDirectoryUserNotExistsException($owner);
        }

        $group = $this->graphRepository->createGroup($groupName);
        $this->graphRepository->subscribeMemberInGroup($group, $azureActiveDirectoryUserIdentifier);

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
        $this->graphRepository->updateGroup($groupId, $groupName);
    }

    /**
     * Adds a member to a group.
     * Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureActiveDirectoryUserNotExistsException
     */
    public function addMemberToGroup($groupId, User $user)
    {
        if (! $this->isMemberOfGroup($groupId, $user))
        {
            $azureActiveDirectoryUserIdentifier = $this->getAzureActiveDirectoryUserIdentifier($user);

            if (empty($azureActiveDirectoryUserIdentifier))
            {
                throw new AzureActiveDirectoryUserNotExistsException($user);
            }

            $this->graphRepository->subscribeMemberInGroup($groupId, $azureActiveDirectoryUserIdentifier);
        }
    }

    /**
     * Removes a member from a group.
     * Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureActiveDirectoryUserNotExistsException
     */
    public function removeMemberFromGroup($groupId, User $user)
    {
        if ($this->isMemberOfGroup($groupId, $user))
        {
            $azureActiveDirectoryUserIdentifier = $this->getAzureActiveDirectoryUserIdentifier($user);

            if (empty($azureActiveDirectoryUserIdentifier))
            {
                throw new AzureActiveDirectoryUserNotExistsException($user);
            }

            $this->graphRepository->removeMemberFromGroup($groupId, $azureActiveDirectoryUserIdentifier);
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
        $azureActiveDirectoryUserIdentifier = $this->getAzureActiveDirectoryUserIdentifier($user);
        if (empty($azureActiveDirectoryUserIdentifier))
        {
            return false;
        }

        $groupMember = $this->graphRepository->getGroupMember($groupId, $azureActiveDirectoryUserIdentifier);

        return $groupMember instanceof \Microsoft\Graph\Model\User;
    }

    /**
     * Returns a list of external user identifiers that are subscribed as member in an Azure AD group
     *
     * @param string $groupId
     *
     * @return string[]
     */
    public function getGroupMembers($groupId)
    {
        $userIdentifiers = [];

        $groupMembers = $this->graphRepository->listGroupMembers($groupId);
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
            $this->graphRepository->removeMemberFromGroup($groupId, $groupMember);
        }
    }

    /**
     * Adds a owner to a group.
     * Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureActiveDirectoryUserNotExistsException
     */
    public function addOwnerToGroup($groupId, User $user)
    {
        if (! $this->isOwnerOfGroup($groupId, $user))
        {
            $azureActiveDirectoryUserIdentifier = $this->getAzureActiveDirectoryUserIdentifier($user);

            if (empty($azureActiveDirectoryUserIdentifier))
            {
                throw new AzureActiveDirectoryUserNotExistsException($user);
            }

            $this->graphRepository->subscribeOwnerInGroup($groupId, $azureActiveDirectoryUserIdentifier);
        }
    }

    /**
     * Removes a owner from a group.
     * Checking if the user is subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureActiveDirectoryUserNotExistsException
     */
    public function removeOwnerFromGroup($groupId, User $user)
    {
        if ($this->isOwnerOfGroup($groupId, $user))
        {
            $azureActiveDirectoryUserIdentifier = $this->getAzureActiveDirectoryUserIdentifier($user);

            if (empty($azureActiveDirectoryUserIdentifier))
            {
                throw new AzureActiveDirectoryUserNotExistsException($user);
            }

            $this->graphRepository->removeOwnerFromGroup($groupId, $azureActiveDirectoryUserIdentifier);
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
        $azureActiveDirectoryUserIdentifier = $this->getAzureActiveDirectoryUserIdentifier($user);

        if (empty($azureActiveDirectoryUserIdentifier))
        {
            return false;
        }

        $groupOwner = $this->graphRepository->getGroupOwner($groupId, $azureActiveDirectoryUserIdentifier);

        return $groupOwner instanceof \Microsoft\Graph\Model\User;
    }

    /**
     * Returns a list of external user identifiers that are subscribed as owner in an Azure AD group
     *
     * @param string $groupId
     *
     * @return string[]
     */
    public function getGroupOwners($groupId)
    {
        $userIdentifiers = [];

        $groupOwners = $this->graphRepository->listGroupOwners($groupId);
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
            $this->graphRepository->removeOwnerFromGroup($groupId, $groupOwner);
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

        foreach ($this->graphRepository->listGroupPlans($groupId) as $groupPlan)
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
        $groupPlans = $this->graphRepository->listGroupPlans($groupId);

        if (empty($groupPlans))
        {
            return null;
        }

        return $groupPlans[0]->getId();
    }

    /**
     * Returns the identifier in azure active directory for a given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function getAzureActiveDirectoryUserIdentifier(User $user)
    {
        $azureActiveDirectoryUserIdentifier = $this->localSetting->get(
            'external_user_id',
            'Chamilo\Libraries\Protocol\Microsoft\Graph',
            $user);

        if (empty($azureActiveDirectoryUserIdentifier))
        {
            $azureActiveDirectoryUser = $this->graphRepository->getAzureActiveDirectoryUser($user);

            if ($azureActiveDirectoryUser instanceof \Microsoft\Graph\Model\User)
            {
                $azureActiveDirectoryUserIdentifier = $azureActiveDirectoryUser->getId();
            }

            $this->localSetting->create(
                'external_user_id',
                $azureActiveDirectoryUserIdentifier,
                'Chamilo\Libraries\Protocol\Microsoft\Graph',
                $user);
        }

        return $azureActiveDirectoryUserIdentifier;
    }

    /**
     * Authorizes a user by a given authorization code
     *
     * @param string $authorizationCode
     */
    public function authorizeUserByAuthorizationCode($authorizationCode)
    {
        $this->graphRepository->authorizeUserByAuthorizationCode($authorizationCode);
    }
}