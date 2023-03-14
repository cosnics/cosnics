<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceUserDefault;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Workspace\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceRepository
{
    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function countAllWorkspaces(): int
    {
        return $this->getDataClassRepository()->count(Workspace::class, new DataClassCountParameters());
    }

    /**
     * @param int[][] $entities
     */
    public function countSharedWorkspacesForEntities(array $entities): int
    {
        return $this->getDataClassRepository()->count(
            Workspace::class, new DataClassCountParameters(
                $this->getSharedWorkspacesForEntitiesWithRightCondition($entities),
                new Joins([$this->getSharedWorkspacesJoin()])
            )
        );
    }

    /**
     * @param int[][] $entities
     */
    public function countWorkspaceFavouritesByUser(User $user, array $entities): int
    {
        return $this->getDataClassRepository()->count(
            Workspace::class, new DataClassCountParameters(
                $this->getWorkspaceFavouritesByUserCondition($user, $entities, RightsService::RIGHT_VIEW),
                $this->getWorkspaceFavouritesByUserJoins(), new RetrieveProperties(
                    [
                        new FunctionConditionVariable(
                            FunctionConditionVariable::DISTINCT,
                            new PropertyConditionVariable(Workspace::class, DataClass::PROPERTY_ID)
                        )
                    ]
                )
            )
        );
    }

    public function countWorkspacesByCreator(User $user): int
    {
        return $this->getDataClassRepository()->count(
            Workspace::class, new DataClassCountParameters($this->getWorkspacesByCreatorCondition($user))
        );
    }

    /**
     * @param int[][] $entities
     */
    public function countWorkspacesForUser(User $user, array $entities, $right): int
    {
        return $this->countWorkspacesForUserWithCondition($user, $entities, $right);
    }

    protected function countWorkspacesForUserWithCondition(
        User $user, array $entities, int $right, ?Condition $condition = null
    ): int
    {
        return $this->getDataClassRepository()->count(
            Workspace::class, new DataClassCountParameters(
                $this->getWorkspaceByUserCondition($user, $entities, $right, $condition),
                new Joins([$this->getSharedWorkspacesJoin(Join::TYPE_LEFT)])
            )
        );
    }

    /**
     * Counts the number of workspaces to which a given user has right with the possibility to exclude workspaces
     * based on their identifiers
     *
     * @param int[][] $entities
     */
    public function countWorkspacesForUserWithExcludedWorkspaces(
        User $user, array $entities, int $right, array $excludedWorkspaceIdentifiers = []
    ): int
    {
        $condition = $this->getExcludedWorkspacesCondition($excludedWorkspaceIdentifiers);

        return $this->countWorkspacesForUserWithCondition($user, $entities, $right, $condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createWorkspace(Workspace $workspace): bool
    {
        return $this->getDataClassRepository()->create($workspace);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createWorkspaceUserDefault(WorkspaceUserDefault $workspaceUserDefault): bool
    {
        return $this->getDataClassRepository()->create($workspaceUserDefault);
    }

    public function deleteWorkspace(Workspace $workspace): bool
    {
        return $this->getDataClassRepository()->delete($workspace);
    }

    /**
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findAllWorkspaces(?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            Workspace::class, new DataClassRetrievesParameters(null, $limit, $offset, $orderProperty)
        );
    }

    /**
     * @param int[][] $entities
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findSharedWorkspacesForEntities(
        array $entities, ?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            Workspace::class, new DataClassRetrievesParameters(
                $this->getSharedWorkspacesForEntitiesWithRightCondition($entities), $limit, $offset, $orderProperty,
                new Joins([$this->getSharedWorkspacesJoin()])
            )
        );
    }

    public function findWorkspaceByIdentifier(string $identifier): ?Workspace
    {
        return $this->getDataClassRepository()->retrieveById(Workspace::class, $identifier);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int[][] $entities
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findWorkspaceFavouritesByUser(
        User $user, array $entities, ?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            Workspace::class, new DataClassRetrievesParameters(
                $this->getWorkspaceFavouritesByUserCondition($user, $entities, RightsService::RIGHT_VIEW), $limit,
                $offset, $orderProperty, $this->getWorkspaceFavouritesByUserJoins(), null, null, new RetrieveProperties(
                    [
                        new FunctionConditionVariable(
                            FunctionConditionVariable::DISTINCT, new PropertiesConditionVariable(Workspace::class)
                        )
                    ]
                )
            )
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
    public function findWorkspaceFavouritesByUserFast(
        User $user, ?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        $joins = new Joins();
        $joins->add($this->getFavouritesJoin());

        $condition = new EqualityCondition(
            new PropertyConditionVariable(WorkspaceUserFavourite::class, WorkspaceUserFavourite::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        return $this->getDataClassRepository()->retrieves(
            Workspace::class, new DataClassRetrievesParameters(
                $condition, $limit, $offset, $orderProperty, $joins
            )
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
    public function findWorkspacesByCreator(
        User $user, ?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            Workspace::class, new DataClassRetrievesParameters(
                $this->getWorkspacesByCreatorCondition($user), $limit, $offset, $orderProperty
            )
        );
    }

    /**
     * @param string[] $identifiers
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findWorkspacesByIdentifiers(
        array $identifiers, ?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Workspace::class, DataClass::PROPERTY_ID), $identifiers
        );

        return $this->getDataClassRepository()->retrieves(
            Workspace::class, new DataClassRetrievesParameters($condition, $limit, $offset, $orderProperty)
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int[][] $entities
     * @param int $right
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findWorkspacesForUser(
        User $user, array $entities, int $right, ?int $limit = null, ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        return $this->retrieveWorkspacesForUser($user, $entities, $right, null, $limit, $offset, $orderProperty);
    }

    /**
     * Finds a list of workspace to which a given user has right with the possibility to exclude workspaces
     * based on their identifiers
     *
     * @param User $user
     * @param int[][] $entities
     * @param int $right
     * @param string[] $excludedWorkspaceIdentifiers
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findWorkspacesForUserWithExcludedWorkspaces(
        User $user, array $entities, int $right, array $excludedWorkspaceIdentifiers = [], ?int $limit = null,
        ?int $offset = null, ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        $condition = $this->getExcludedWorkspacesCondition($excludedWorkspaceIdentifiers);

        return $this->retrieveWorkspacesForUser($user, $entities, $right, $condition, $limit, $offset, $orderProperty);
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * Helper function to get a condition for the excluded workspaces
     *
     * @param string[] $excludedWorkspaceIdentifiers
     */
    protected function getExcludedWorkspacesCondition(array $excludedWorkspaceIdentifiers): NotCondition
    {
        return new NotCondition(
            new InCondition(
                new PropertyConditionVariable(Workspace::class, DataClass::PROPERTY_ID), $excludedWorkspaceIdentifiers
            )
        );
    }

    private function getFavouritesJoin(int $joinType = Join::TYPE_NORMAL): Join
    {
        return new Join(
            WorkspaceUserFavourite::class, new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceUserFavourite::class, WorkspaceUserFavourite::PROPERTY_WORKSPACE_ID
            ), new PropertyConditionVariable(Workspace::class, DataClass::PROPERTY_ID)
        ), $joinType
        );
    }

    /**
     * @param int[][] $entities
     */
    private function getSharedWorkspacesForEntitiesWithRightCondition(
        array $entities, int $right = RightsService::RIGHT_VIEW
    ): OrCondition
    {
        $conditions = [];

        foreach ($entities as $entityType => $entityIdentifiers)
        {
            $entityConditions = [];

            $entityConditions[] = new InCondition(
                new PropertyConditionVariable(
                    WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_ENTITY_ID
                ), $entityIdentifiers
            );
            $entityConditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_ENTITY_TYPE
                ), new StaticConditionVariable($entityType)
            );
            $entityConditions[] = new EqualityCondition(
                new OperationConditionVariable(
                    new PropertyConditionVariable(
                        WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_RIGHTS
                    ), OperationConditionVariable::BITWISE_AND, new StaticConditionVariable($right)
                ), new StaticConditionVariable($right)
            );

            $conditions[] = new AndCondition($entityConditions);
        }

        return new OrCondition($conditions);
    }

    /**
     * @param int $joinType
     *
     * @return \Chamilo\Libraries\Storage\Query\Join
     */
    private function getSharedWorkspacesJoin(int $joinType = Join::TYPE_NORMAL): Join
    {
        return new Join(
            WorkspaceEntityRelation::class, new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_WORKSPACE_ID
            ), new PropertyConditionVariable(Workspace::class, DataClass::PROPERTY_ID)
        ), $joinType
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int[][] $entities
     * @param int $right
     * @param ?Condition $additionalCondition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    private function getWorkspaceByUserCondition(
        User $user, array $entities, int $right, Condition $additionalCondition = null
    ): AndCondition
    {
        $orConditions = [];

        $orConditions[] = $this->getWorkspacesByCreatorCondition($user);
        $orConditions[] = $this->getSharedWorkspacesForEntitiesWithRightCondition($entities, $right);

        $conditions = [];
        $conditions[] = new OrCondition($orConditions);

        if ($additionalCondition)
        {
            $conditions[] = $additionalCondition;
        }

        return new AndCondition($conditions);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int[][] $entities
     * @param int $right
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    private function getWorkspaceFavouritesByUserCondition(User $user, array $entities, int $right): AndCondition
    {
        $andConditions = [];

        if (!$user->is_platform_admin())
        {
            $andConditions[] = $this->getWorkspaceByUserCondition($user, $entities, $right);
        }

        $andConditions[] = new EqualityCondition(
            new PropertyConditionVariable(WorkspaceUserFavourite::class, WorkspaceUserFavourite::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        return new AndCondition($andConditions);
    }

    private function getWorkspaceFavouritesByUserJoins(): Joins
    {
        $joins = new Joins();

        $joins->add($this->getSharedWorkspacesJoin(Join::TYPE_LEFT));
        $joins->add($this->getFavouritesJoin());

        return $joins;
    }

    private function getWorkspacesByCreatorCondition(User $user): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_CREATOR_ID),
            new StaticConditionVariable($user->getId())
        );
    }

    public function retrieveDefaultWorkspaceForUserIdentifier(string $userIdentifier): ?Workspace
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(WorkspaceUserDefault::class, WorkspaceUserDefault::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        $join = new Join(
            WorkspaceUserDefault::class, new EqualityCondition(
                new PropertyConditionVariable(Workspace::class, DataClass::PROPERTY_ID),
                new PropertyConditionVariable(WorkspaceUserDefault::class, WorkspaceUserDefault::PROPERTY_WORKSPACE_ID)
            )
        );

        return $this->getDataClassRepository()->retrieve(
            Workspace::class, new DataClassRetrieveParameters($condition, null, new Joins([$join]))
        );
    }

    public function retrieveWorkspaceUserDefaultForUserIdentifier(string $userIdentifier): ?WorkspaceUserDefault
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(WorkspaceUserDefault::class, WorkspaceUserDefault::PROPERTY_USER_ID),
            new StaticConditionVariable($userIdentifier)
        );

        return $this->getDataClassRepository()->retrieve(
            WorkspaceUserDefault::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * Helper function to retrieve workspaces for a given user with a given condition
     *
     * @param User $user
     * @param int[][] $entities
     * @param int $right
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function retrieveWorkspacesForUser(
        User $user, array $entities, int $right, ?Condition $condition = null, ?int $limit = null, ?int $offset = null,
        ?OrderBy $orderProperty = null
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            Workspace::class, new DataClassRetrievesParameters(
                $this->getWorkspaceByUserCondition($user, $entities, $right, $condition), $limit, $offset,
                $orderProperty, new Joins([$this->getSharedWorkspacesJoin(Join::TYPE_LEFT)])
            )
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateWorkspace(Workspace $workspace): bool
    {
        return $this->getDataClassRepository()->update($workspace);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateWorkspaceUserDefault(WorkspaceUserDefault $workspaceUserDefault): bool
    {
        return $this->getDataClassRepository()->update($workspaceUserDefault);
    }
}