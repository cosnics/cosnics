<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Service;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\RightsRepository;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;

class RightsService
{

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    private $userService;

    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    private $groupService;

    /**
     * @var \Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\RightsRepository
     */
    private $rightsRepository;

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\RightsRepository $rightsRepository
     */
    public function __construct(UserService $userService, GroupService $groupService, RightsRepository $rightsRepository
    )
    {
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->rightsRepository = $rightsRepository;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return integer
     */
    public function countPublicationGroupsForPublication(Publication $publication)
    {
        return $this->getRightsRepository()->countPublicationGroupsForPublication($publication);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return integer
     */
    public function countPublicationUsersForPublication(Publication $publication)
    {
        return $this->getRightsRepository()->countPublicationUsersForPublication($publication);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup $publicationGroup
     *
     * @return boolean
     */
    public function createPublicationGroup(PublicationGroup $publicationGroup)
    {
        return $this->getRightsRepository()->createPublicationGroup($publicationGroup);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer $groupIdentifier
     *
     * @return boolean
     */
    public function createPublicationGroupForPublicationAndGroupIdentifier(
        Publication $publication, int $groupIdentifier
    )
    {
        $publicationGroup = new PublicationGroup();
        $publicationGroup->set_publication($publication->getId());
        $publicationGroup->set_group_id($groupIdentifier);

        return $this->createPublicationGroup($publicationGroup);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer[] $groupIdentifiers
     *
     * @return boolean
     */
    public function createPublicationGroupsForPublicationAndGroupIdentifiers(
        Publication $publication, array $groupIdentifiers
    )
    {
        foreach ($groupIdentifiers as $groupIdentifier)
        {
            if (!$this->createPublicationGroupForPublicationAndGroupIdentifier($publication, $groupIdentifier))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer[] $userIdentifiers
     * @param integer[] $groupIdentifiers
     *
     * @return boolean
     */
    public function createPublicationRightsForPublicationAndUserAndGroupIdentifiers(
        Publication $publication, array $userIdentifiers, array $groupIdentifiers
    )
    {
        if (!$this->createPublicationUsersForPublicationAndUserIdentifiers($publication, $userIdentifiers))
        {
            return false;
        }

        if (!$this->createPublicationGroupsForPublicationAndGroupIdentifiers($publication, $groupIdentifiers))
        {
            return false;
        }

        return true;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser $publicationUser
     *
     * @return boolean
     */
    public function createPublicationUser(PublicationUser $publicationUser)
    {
        return $this->getRightsRepository()->createPublicationUser($publicationUser);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer $userIdentifier
     *
     * @return boolean
     */
    public function createPublicationUserForPublicationAndUserIdentifier(
        Publication $publication, int $userIdentifier
    )
    {
        $publicationUser = new PublicationUser();
        $publicationUser->set_publication($publication->getId());
        $publicationUser->set_user($userIdentifier);

        return $this->createPublicationUser($publicationUser);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer[] $userIdentifiers
     *
     * @return boolean
     */
    public function createPublicationUsersForPublicationAndUserIdentifiers(
        Publication $publication, array $userIdentifiers
    )
    {
        foreach ($userIdentifiers as $userIdentifier)
        {
            if (!$this->createPublicationUserForPublicationAndUserIdentifier($publication, $userIdentifier))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function deletePublicationGroupsForPublication(Publication $publication)
    {
        return $this->getRightsRepository()->deletePublicationGroupsForPublication($publication);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer[] $groupIdentifiers
     *
     * @return boolean
     */
    public function deletePublicationGroupsForPublicationAndGroupIdentifiers(
        Publication $publication, array $groupIdentifiers
    )
    {
        return $this->getRightsRepository()->deletePublicationGroupsForPublicationAndGroupIdentifiers(
            $publication, $groupIdentifiers
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function deletePublicationRights(Publication $publication)
    {
        if (!$this->deletePublicationUsersForPublication($publication))
        {
            echo 'deletePublicationUsersForPublication';
            return false;
        }

        if (!$this->deletePublicationGroupsForPublication($publication))
        {
            echo 'deletePublicationGroupsForPublication';
            return false;
        }

        return true;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function deletePublicationUsersForPublication(Publication $publication)
    {
        return $this->getRightsRepository()->deletePublicationUsersForPublication($publication);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer[] $userIdentifiers
     *
     * @return boolean
     */
    public function deletePublicationUsersForPublicationAndUserIdentifiers(
        Publication $publication, array $userIdentifiers
    )
    {
        return $this->getRightsRepository()->deletePublicationUsersForPublicationAndUserIdentifiers(
            $publication, $userIdentifiers
        );
    }

    /**
     * @return \Chamilo\Core\Group\Service\GroupService
     */
    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    /**
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     */
    public function setGroupService(GroupService $groupService): void
    {
        $this->groupService = $groupService;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     */
    public function getGroupsForPublication(Publication $publication)
    {
        return $this->getGroupService()->findGroupsByIdentifiers(
            $this->getPublicationGroupIdentifiersForPublication($publication)
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return integer[]
     */
    public function getPublicationGroupIdentifiersForPublication(Publication $publication)
    {
        return $this->getRightsRepository()->getPublicationGroupIdentifiersForPublication($publication);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup[]
     */
    public function getPublicationGroupsForPublication(Publication $publication)
    {
        return $this->getRightsRepository()->getPublicationGroupsForPublication($publication);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return integer[]
     */
    public function getPublicationUserIdentifiersForPublication(Publication $publication)
    {
        return $this->getRightsRepository()->getPublicationUserIdentifiersForPublication($publication);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser[]
     */
    public function getPublicationUsersForPublication(Publication $publication)
    {
        return $this->getRightsRepository()->getPublicationUsersForPublication($publication);
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\RightsRepository
     */
    public function getRightsRepository(): RightsRepository
    {
        return $this->rightsRepository;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\RightsRepository $rightsRepository
     */
    public function setRightsRepository(RightsRepository $rightsRepository): void
    {
        $this->rightsRepository = $rightsRepository;
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getUsersForPublication(Publication $publication)
    {
        return $this->getUserService()->findUsersByIdentifiers(
            $this->getPublicationUserIdentifiersForPublication($publication)
        );
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function isAllowedToEditPublication(Publication $publication, User $user)
    {
        return $user->isPlatformAdmin() || $publication->get_publisher() == $user->getId();
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function isAllowedToDeletePublication(Publication $publication, User $user)
    {
        return $user->isPlatformAdmin() || $publication->get_publisher() == $user->getId();
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function isAllowedToViewPublication(Publication $publication, User $user)
    {
        $isUserPlatformAdmin = $user->isPlatformAdmin();
        $isUserPublisher = $publication->get_publisher() == $user->getId();

        if ($isUserPlatformAdmin || $isUserPublisher)
        {
            return true;
        }

        return $this->isTargetUserForPublication($publication, $user);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function isPublicationSharedWithAnyone(Publication $publication)
    {
        return $this->isPublicationSharedWithUsers($publication) || $this->isPublicationSharedWithGroups($publication);
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function isPublicationSharedWithGroups(Publication $publication)
    {
        return $this->countPublicationGroupsForPublication($publication) > 0;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function isPublicationSharedWithUsers(Publication $publication)
    {
        return $this->countPublicationUsersForPublication($publication) > 0;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function isTargetUserForPublication(Publication $publication, User $user)
    {
        if ($this->isPublicationNotSharedWithAnyone($publication))
        {
            return false;
        }

        if (in_array($user->getId(), $this->getPublicationUserIdentifiersForPublication($publication)))
        {
            return true;
        }
        else
        {
            // TODO: This should eventually use the relevant User or Group service
            $userGroupIdentifiers = $user->get_groups(true);
            $publicationGroupIdentifiers = $this->getPublicationGroupIdentifiersForPublication($publication);

            $commonGroupIdentifiers = array_intersect($userGroupIdentifiers, $publicationGroupIdentifiers);

            if (count($commonGroupIdentifiers) > 0)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer[] $groupIdentifiers
     *
     * @return boolean
     */
    public function updatePublicationGroupsForPublicationAndGroupIdentifiers(
        Publication $publication, array $groupIdentifiers
    )
    {
        $currentGroupIdentifiers = $this->getPublicationGroupIdentifiersForPublication($publication);

        $newGroupIdentifiers = array_diff($groupIdentifiers, $currentGroupIdentifiers);
        $oldGroupIdentifiers = array_diff($currentGroupIdentifiers, $groupIdentifiers);

        if (!$this->createPublicationGroupsForPublicationAndGroupIdentifiers($publication, $newGroupIdentifiers))
        {
            return false;
        }

        if (!$this->deletePublicationGroupsForPublicationAndGroupIdentifiers($publication, $oldGroupIdentifiers))
        {
            return false;
        }

        return true;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer[] $userIdentifiers
     * @param integer[] $groupIdentifiers
     *
     * @return boolean
     */
    public function updatePublicationRightsForPublicationAndUserAndGroupIdentifiers(
        Publication $publication, array $userIdentifiers, array $groupIdentifiers
    )
    {
        if (!$this->updatePublicationUsersForPublicationAndUserIdentifiers($publication, $userIdentifiers))
        {
            return false;
        }

        if (!$this->updatePublicationGroupsForPublicationAndGroupIdentifiers($publication, $groupIdentifiers))
        {
            return false;
        }

        return true;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     * @param integer[] $userIdentifiers
     *
     * @return boolean
     */
    public function updatePublicationUsersForPublicationAndUserIdentifiers(
        Publication $publication, array $userIdentifiers
    )
    {
        $currentUserIdentifiers = $this->getPublicationUserIdentifiersForPublication($publication);

        $newUserIdentifiers = array_diff($userIdentifiers, $currentUserIdentifiers);
        $oldUserIdentifiers = array_diff($currentUserIdentifiers, $userIdentifiers);

        if (count($newUserIdentifiers) > 0)
        {
            if (!$this->createPublicationUsersForPublicationAndUserIdentifiers($publication, $newUserIdentifiers))
            {
                return false;
            }
        }

        if (count($oldUserIdentifiers) > 0)
        {
            if (!$this->deletePublicationUsersForPublicationAndUserIdentifiers($publication, $oldUserIdentifiers))
            {
                return false;
            }
        }

        return true;
    }

}