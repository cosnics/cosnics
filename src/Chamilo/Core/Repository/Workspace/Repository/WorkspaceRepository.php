<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceRepository
{

    /**
     *
     * @return integer
     */
    public function countAllWorkspaces()
    {
        return DataManager::count(Workspace::class);
    }

    /**
     *
     * @param integer[] $entities
     *
     * @return integer
     */
    public function countSharedWorkspacesForEntities($entities)
    {
        return DataManager::count(
            Workspace::class, new DataClassCountParameters(
                $this->getSharedWorkspacesForEntitiesWithRightCondition($entities),
                new Joins(array($this->getSharedWorkspacesJoin()))
            )
        );
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     *
     * @return integer
     */
    public function countWorkspaceFavouritesByUser(User $user, $entities)
    {
        return DataManager::count(
            Workspace::class, new DataClassCountParameters(
                $this->getWorkspaceFavouritesByUserCondition($user, $entities, RightsService::RIGHT_VIEW),
                $this->getWorkspaceFavouritesByUserJoins(), new DataClassProperties(
                    array(
                        new FunctionConditionVariable(
                            FunctionConditionVariable::DISTINCT,
                            new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_ID)
                        )
                    )
                )
            )
        );
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     */
    public function countWorkspacesByCreator(User $user)
    {
        return DataManager::count(
            Workspace::class, new DataClassCountParameters($this->getWorkspacesByCreatorCondition($user))
        );
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     *
     * @return integer
     */
    public function countWorkspacesForUser(User $user, $entities, $right)
    {
        return $this->countWorkspacesForUserWithCondition($user, $entities, $right);
    }

    /**
     * Helper function to count workspaces with a given condition
     *
     * @param User $user
     * @param integer[] $entities
     * @param int $right
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    protected function countWorkspacesForUserWithCondition(User $user, $entities, $right, Condition $condition = null)
    {
        return DataManager::count(
            Workspace::class, new DataClassCountParameters(
                $this->getWorkspaceByUserCondition($user, $entities, $right, $condition),
                new Joins(array($this->getSharedWorkspacesJoin(Join::TYPE_LEFT)))
            )
        );
    }

    /**
     * Counts the number of workspaces to which a given user has right with the possibility to exclude workspaces
     * based on their identifiers
     *
     * @param User $user
     * @param integer[] $entities
     * @param int $right
     * @param int[] $excludedWorkspaceIdentifiers
     *
     * @return int
     */
    public function countWorkspacesForUserWithExcludedWorkspaces(
        User $user, $entities, $right, $excludedWorkspaceIdentifiers
    )
    {
        $condition = $this->getExcludedWorkspacesCondition($excludedWorkspaceIdentifiers);

        return $this->countWorkspacesForUserWithCondition($user, $entities, $right, $condition);
    }

    /**
     *
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     */
    public function findAllWorkspaces($limit = null, $offset = null, $orderProperty = null)
    {
        return DataManager::retrieves(
            Workspace::class, new DataClassRetrievesParameters(null, $limit, $offset, $orderProperty)
        );
    }

    /**
     *
     * @param integer[] $entities
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     */
    public function findSharedWorkspacesForEntities($entities, $limit = null, $offset = null, $orderProperty = null)
    {
        return DataManager::retrieves(
            Workspace::class, new DataClassRetrievesParameters(
                $this->getSharedWorkspacesForEntitiesWithRightCondition($entities), $limit, $offset, $orderProperty,
                new Joins(array($this->getSharedWorkspacesJoin()))
            )
        );
    }

    /**
     *
     * @param integer $identifier
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function findWorkspaceByIdentifier($identifier)
    {
        return DataManager::retrieve_by_id(Workspace::class, $identifier);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     */
    public function findWorkspaceFavouritesByUser(
        User $user, $entities, $limit = null, $offset = null, $orderProperty = null
    )
    {
        return DataManager::retrieves(
            Workspace::class, new DataClassRetrievesParameters(
                $this->getWorkspaceFavouritesByUserCondition($user, $entities, RightsService::RIGHT_VIEW), $limit,
                $offset, $orderProperty, $this->getWorkspaceFavouritesByUserJoins(), true
            )
        );
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     */
    public function findWorkspaceFavouritesByUserFast(
        User $user, $limit = null, $offset = null, $orderProperty = null
    )
    {
        $joins = new Joins();
        $joins->add($this->getFavouritesJoin());

        $condition = new EqualityCondition(
            new PropertyConditionVariable(WorkspaceUserFavourite::class, WorkspaceUserFavourite::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        return DataManager::retrieves(
            Workspace::class, new DataClassRetrievesParameters(
                $condition, $limit, $offset, $orderProperty, $joins, true
            )
        );
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     */
    public function findWorkspacesByCreator(User $user, $limit = null, $offset = null, $orderProperty = null)
    {
        return DataManager::retrieves(
            Workspace::class, new DataClassRetrievesParameters(
                $this->getWorkspacesByCreatorCondition($user), $limit, $offset, $orderProperty
            )
        );
    }

    /**
     *
     * @param integer[] $identifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     */
    public function findWorkspacesByIdentifiers($identifiers, $limit = null, $offset = null, $orderProperty = null)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_ID), $identifiers
        );

        return DataManager::retrieves(
            Workspace::class, new DataClassRetrievesParameters($condition, $limit, $offset, $orderProperty)
        );
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     * @param integer $right
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     */
    public function findWorkspacesForUser(User $user, $entities, $right, $limit, $offset, $orderProperty = null)
    {
        return $this->retrieveWorkspacesForUser($user, $entities, $right, null, $limit, $offset, $orderProperty);
    }

    /**
     * Finds a list of workspace to which a given user has right with the possibility to exclude workspaces
     * based on their identifiers
     *
     * @param User $user
     * @param integer[] $entities
     * @param int $right
     * @param int[] $excludedWorkspaceIdentifiers
     * @param int $limit
     * @param int $offset
     * @param OrderProperty $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     */
    public function findWorkspacesForUserWithExcludedWorkspaces(
        User $user, $entities, $right, $excludedWorkspaceIdentifiers, $limit, $offset, $orderProperty = null
    )
    {
        $condition = $this->getExcludedWorkspacesCondition($excludedWorkspaceIdentifiers);

        return $this->retrieveWorkspacesForUser($user, $entities, $right, $condition, $limit, $offset, $orderProperty);
    }

    /**
     * Helper function to get a condition for the excluded workspaces
     *
     * @param int[] $excludedWorkspaceIdentifiers
     *
     * @return NotCondition
     */
    protected function getExcludedWorkspacesCondition($excludedWorkspaceIdentifiers)
    {
        $condition = new NotCondition(
            new InCondition(
                new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_ID), $excludedWorkspaceIdentifiers
            )
        );

        return $condition;
    }

    /**
     *
     * @param integer $joinType
     *
     * @return \Chamilo\Libraries\Storage\Query\Join
     */
    private function getFavouritesJoin($joinType = Join::TYPE_NORMAL)
    {
        return new Join(
            WorkspaceUserFavourite::class, new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceUserFavourite::class, WorkspaceUserFavourite::PROPERTY_WORKSPACE_ID
            ), new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_ID)
        ), $joinType
        );
    }

    /**
     *
     * @param integer[] $entities
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    private function getSharedWorkspacesForEntitiesWithRightCondition($entities, $right = RightsService::RIGHT_VIEW)
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
     *
     * @param integer $joinType
     *
     * @return \Chamilo\Libraries\Storage\Query\Join
     */
    private function getSharedWorkspacesJoin($joinType = Join::TYPE_NORMAL)
    {
        return new Join(
            WorkspaceEntityRelation::class, new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_WORKSPACE_ID
            ), new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_ID)
        ), $joinType
        );
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     * @param int $right
     * @param Condition $additionalCondition
     *
     * @return OrCondition
     */
    private function getWorkspaceByUserCondition(User $user, $entities, $right, Condition $additionalCondition = null)
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
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    private function getWorkspaceFavouritesByUserCondition(User $user, $entities, $right)
    {
        $andConditions = [];

        $andConditions[] = $this->getWorkspaceByUserCondition($user, $entities, $right);
        $andConditions[] = new EqualityCondition(
            new PropertyConditionVariable(WorkspaceUserFavourite::class, WorkspaceUserFavourite::PROPERTY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        return new AndCondition($andConditions);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    private function getWorkspaceFavouritesByUserJoins()
    {
        $joins = new Joins();
        $joins->add($this->getSharedWorkspacesJoin(Join::TYPE_LEFT));
        $joins->add($this->getFavouritesJoin());

        return $joins;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    private function getWorkspacesByCreatorCondition(User $user)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_CREATOR_ID),
            new StaticConditionVariable($user->getId())
        );
    }

    /**
     * Helper function to retrieve workspaces for a given user with a given condition
     *
     * @param User $user
     * @param integer[] $entities
     * @param int $right
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $limit
     * @param int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderProperty $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     */
    protected function retrieveWorkspacesForUser(
        User $user, $entities, $right, Condition $condition = null, $limit = null, $offset = null, $orderProperty = null
    )
    {
        return DataManager::retrieves(
            Workspace::class, new DataClassRetrievesParameters(
                $this->getWorkspaceByUserCondition($user, $entities, $right, $condition), $limit, $offset,
                $orderProperty, new Joins(array($this->getSharedWorkspacesJoin(Join::TYPE_LEFT))), true
            )
        );
    }
}