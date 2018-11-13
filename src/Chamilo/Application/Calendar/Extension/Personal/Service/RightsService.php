<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Service;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationGroup;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser;
use Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\RightsRepository;
use Chamilo\Core\User\Storage\DataClass\User;

class RightsService
{
    /**
     * @var \Chamilo\Application\Calendar\Extension\Personal\Storage\Repository\RightsRepository
     */
    private $rightsRepository;

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
     *
     * @return boolean
     */
    public function deletePublicationRights(Publication $publication)
    {
        if (!$this->deletePublicationUsersForPublication($publication))
        {
            return false;
        }

        if (!$this->deletePublicationGroupsForPublication($publication))
        {
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
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication $publication
     *
     * @return boolean
     */
    public function isPublicationNotSharedWithAnyone(Publication $publication)
    {
        return $this->isPublicationSharedWithUsers() || $this->isPublicationSharedWithGroups();
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

        if (in_array($user->getId(), $this->getPublicationUserIdentifiersForPublication()))
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
     * @param integer[] $userIdentifiers
     * @param integer[] $groupIdentifiers
     *
     * @return boolean
     */
    public function createPublicationRightsForPublicationAndUserAndGroupIdentifiers(
        Publication $publication, array $userIdentifiers, array $groupIdentifiers
    )
    {
        if (!$this->createPublicationUserForPublicationAndUserIdentifiers($publication, $userIdentifiers))
        {
            return false;
        }

        if (!$this->createPublicationGroupForPublicationAndGroupIdentifiers($publication, $groupIdentifiers))
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
    public function createPublicationUserForPublicationAndUserIdentifiers(
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
     * @param integer[] $groupIdentifiers
     *
     * @return boolean
     */
    public function createPublicationGroupForPublicationAndGroupIdentifiers(
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
     * @param \Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\PublicationUser $publicationUser
     *
     * @return boolean
     */
    public function createPublicationUser(PublicationUser $publicationUser)
    {
        return $this->getRightsRepository()->createPublicationUser($publicationUser);
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

}