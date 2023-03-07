<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

/**
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceService
{
    public const TYPE_PERSONAL = 1;
    public const TYPE_WORKSPACE = 2;

    protected EntityService $entityService;

    protected UserService $userService;

    private WorkspaceRepository $workspaceRepository;

    public function __construct(
        UserService $userService, EntityService $entityService, WorkspaceRepository $workspaceRepository
    )
    {
        $this->userService = $userService;
        $this->entityService = $entityService;
        $this->workspaceRepository = $workspaceRepository;
    }

    public function countAllWorkspaces(): int
    {
        return $this->getWorkspaceRepository()->countAllWorkspaces();
    }

    public function countSharedWorkspacesForUser(User $user): int
    {
        return $this->getWorkspaceRepository()->countSharedWorkspacesForEntities($this->getEntitiesForUser($user));
    }

    public function countWorkspaceFavouritesByUser(User $user): int
    {
        return $this->getWorkspaceRepository()->countWorkspaceFavouritesByUser(
            $user, $this->getEntityService()->getEntitiesForUser($user)
        );
    }

    public function countWorkspacesByCreator(User $user): int
    {
        return $this->getWorkspaceRepository()->countWorkspacesByCreator($user);
    }

    public function countWorkspacesForUser(User $user, $right = RightsService::RIGHT_VIEW): int
    {
        return $this->getWorkspaceRepository()->countWorkspacesForUser($user, $this->getEntitiesForUser($user), $right);
    }

    /**
     * Counts the number of workspaces to which a given user has right with the possibility to exclude workspaces
     * based on their identifiers
     */
    public function countWorkspacesForUserWithExcludedWorkspaces(
        User $user, int $right = RightsService::RIGHT_VIEW, array $excludedWorkspaceIdentifiers = []
    ): int
    {
        return $this->getWorkspaceRepository()->countWorkspacesForUserWithExcludedWorkspaces(
            $user, $this->getEntitiesForUser($user), $right, $excludedWorkspaceIdentifiers
        );
    }

    /**
     * @param string[] $workspaceProperties
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createWorkspace(array $workspaceProperties): ?Workspace
    {
        $workspace = new Workspace();
        $this->setWorkspaceProperties($workspace, $workspaceProperties);

        if (!$this->getWorkspaceRepository()->createWorkspace($workspace))
        {
            return null;
        }

        return $workspace;
    }

    public function deleteWorkspace(Workspace $workspace): bool
    {
        return $this->getWorkspaceRepository()->deleteWorkspace($workspace);
    }

    public function determineWorkspaceForUserByIdentifier(User $user, ?string $identifier = null): ?WorkspaceInterface
    {
        if (!empty($identifier))
        {
            if (!is_numeric($identifier))
            {
                throw new InvalidArgumentException();
            }

            return $this->getWorkspaceByIdentifier($identifier);
        }
        else
        {
            return $this->getPersonalWorkspaceForUser($user);
        }
    }

    /**
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getAllWorkspaces(?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null): ArrayCollection
    {
        return $this->getWorkspaceRepository()->findAllWorkspaces($count, $offset, $orderBy);
    }

    /**
     * @return int[][]
     */
    public function getEntitiesForUser(User $user): array
    {
        $entities = [];

        $entities[UserEntity::ENTITY_TYPE] = [$user->get_id()];
        $entities[PlatformGroupEntity::ENTITY_TYPE] = [];

        $userGroupIdentifiers = $user->get_groups(true);

        foreach ($userGroupIdentifiers as $userGroupIdentifier)
        {
            $entities[PlatformGroupEntity::ENTITY_TYPE][] = $userGroupIdentifier;
        }

        return $entities;
    }

    public function getEntityService(): EntityService
    {
        return $this->entityService;
    }

    public function getPersonalWorkspaceForUser(User $user): PersonalWorkspace
    {
        return new PersonalWorkspace($user);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getSharedWorkspacesForUser(
        User $user, ?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        return $this->getWorkspaceRepository()->findSharedWorkspacesForEntities(
            $this->getEntitiesForUser($user), $limit, $offset, $orderProperty
        );
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function getWorkspaceByIdentifier(string $identifier): ?Workspace
    {
        return $this->getWorkspaceRepository()->findWorkspaceByIdentifier($identifier);
    }

    public function getWorkspaceByTypeAndTypeIdentifier(int $type, string $typeIdentifier): ?WorkspaceInterface
    {
        if ($type == self::TYPE_PERSONAL)
        {
            $user = $this->getUserService()->findUserByIdentifier($typeIdentifier);

            return $this->getPersonalWorkspaceForUser($user);
        }
        else
        {
            return $this->getWorkspaceByIdentifier($typeIdentifier);
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getWorkspaceFavouritesByUser(
        User $user, ?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        if (is_null($orderProperty))
        {
            $orderProperty = new OrderBy([
                new OrderProperty(
                    new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_NAME), SORT_ASC
                )
            ]);
        }

        return $this->getWorkspaceRepository()->findWorkspaceFavouritesByUser(
            $user, $this->getEntityService()->getEntitiesForUser($user), $limit, $offset, $orderProperty
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getWorkspaceFavouritesByUserFast(
        User $user, ?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        if (is_null($orderProperty))
        {
            $orderProperty = new OrderBy([
                new OrderProperty(new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_NAME), SORT_ASC)
            ]);
        }

        return $this->getWorkspaceRepository()->findWorkspaceFavouritesByUserFast(
            $user, $limit, $offset, $orderProperty
        );
    }

    public function getWorkspaceRepository(): WorkspaceRepository
    {
        return $this->workspaceRepository;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getWorkspacesByCreator(
        User $user, ?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        return $this->getWorkspaceRepository()->findWorkspacesByCreator($user, $limit, $offset, $orderProperty);
    }

    /**
     * @param string[] $identifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getWorkspacesByIdentifiers(array $identifiers): ArrayCollection
    {
        return $this->getWorkspaceRepository()->findWorkspacesByIdentifiers($identifiers);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $right
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getWorkspacesForUser(
        User $user, int $right = RightsService::RIGHT_VIEW, ?int $limit = null, ?int $offset = null,
        ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        return $this->getWorkspaceRepository()->findWorkspacesForUser(
            $user, $this->getEntitiesForUser($user), $right, $limit, $offset, $orderProperty
        );
    }

    /**
     * Finds a list of workspace to which a given user has right with the possibility to exclude workspaces
     * based on their identifiers
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $right
     * @param string[] $excludedWorkspaceIdentifiers
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getWorkspacesForUserWithExcludedWorkspaces(
        User $user, int $right = RightsService::RIGHT_VIEW, array $excludedWorkspaceIdentifiers = [],
        ?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        return $this->getWorkspaceRepository()->findWorkspacesForUserWithExcludedWorkspaces(
            $user, $this->getEntitiesForUser($user), $right, $excludedWorkspaceIdentifiers, $limit, $offset,
            $orderProperty
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param string[] $workspaceProperties
     */
    private function setWorkspaceProperties(Workspace $workspace, array $workspaceProperties)
    {
        $workspace->setName($workspaceProperties[Workspace::PROPERTY_NAME]);
        $workspace->setDescription($workspaceProperties[Workspace::PROPERTY_DESCRIPTION]);
        $workspace->setCreationDate($workspaceProperties[Workspace::PROPERTY_CREATION_DATE]);
        $workspace->setCreatorId($workspaceProperties[Workspace::PROPERTY_CREATOR_ID]);
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param string[] $workspaceProperties
     *
     * @return bool
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateWorkspace(Workspace $workspace, array $workspaceProperties): bool
    {
        $this->setWorkspaceProperties($workspace, $workspaceProperties);

        return $this->getWorkspaceRepository()->updateWorkspace($workspace);
    }

    public function userHasWorkspaceFavourites(User $user): bool
    {
        return $this->countWorkspaceFavouritesByUser($user) > 0;
    }
}