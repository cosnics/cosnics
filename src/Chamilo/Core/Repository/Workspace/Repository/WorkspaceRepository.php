<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;

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
     * @param integer $identifier
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function findWorkspaceByIdentifier($identifier)
    {
        return DataManager:: retrieve_by_id(Workspace:: class_name(), $identifier);
    }

    /**
     *
     * @param integer[] $identifiers
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findWorkspacesByIdentifiers($identifiers, $limit = null, $offset = null, $orderProperty = array())
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Workspace:: class_name(), Workspace :: PROPERTY_ID),
            $identifiers
        );

        return DataManager:: retrieves(
            Workspace:: class_name(),
            new DataClassRetrievesParameters($condition, $limit, $offset, $orderProperty)
        );
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findWorkspacesByCreator(User $user, $limit = null, $offset = null, $orderProperty = array())
    {
        return DataManager:: retrieves(
            Workspace:: class_name(),
            new DataClassRetrievesParameters(
                $this->getWorkspacesByCreatorCondition($user),
                $limit,
                $offset,
                $orderProperty
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
        return DataManager:: count(
            Workspace:: class_name(),
            new DataClassCountParameters($this->getWorkspacesByCreatorCondition($user))
        );
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
            new PropertyConditionVariable(Workspace:: class_name(), Workspace :: PROPERTY_CREATOR_ID),
            new StaticConditionVariable($user->getId())
        );
    }

    /**
     *
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAllWorkspaces($limit = null, $offset = null, $orderProperty = array())
    {
        return DataManager:: retrieves(
            Workspace:: class_name(),
            new DataClassRetrievesParameters(null, $limit, $offset, $orderProperty)
        );
    }

    /**
     *
     * @return integer
     */
    public function countAllWorkspaces()
    {
        return DataManager:: count(Workspace:: class_name());
    }

    /**
     *
     * @param integer[] $entities
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findSharedWorkspacesForEntities($entities, $limit = null, $offset = null, $orderProperty = array())
    {
        return DataManager:: retrieves(
            Workspace:: class_name(),
            new DataClassRetrievesParameters(
                $this->getSharedWorkspacesForEntitiesWithRightCondition($entities),
                $limit,
                $offset,
                $orderProperty,
                new Joins(array($this->getSharedWorkspacesJoin()))
            )
        );
    }

    /**
     *
     * @param integer[] $entities
     *
     * @return integer
     */
    public function countSharedWorkspacesForEntities($entities)
    {
        return DataManager:: count(
            Workspace:: class_name(),
            new DataClassCountParameters(
                $this->getSharedWorkspacesForEntitiesWithRightCondition($entities),
                new Joins(array($this->getSharedWorkspacesJoin()))
            )
        );
    }

    /**
     *
     * @param integer[] $entities
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    private function getSharedWorkspacesForEntitiesWithRightCondition($entities, $right = RightsService :: RIGHT_VIEW)
    {
        $conditions = array();

        foreach ($entities as $entityType => $entityIdentifiers)
        {
            foreach ($entityIdentifiers as $entityIdentifier)
            {
                $entityConditions = array();

                $entityConditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        WorkspaceEntityRelation:: class_name(),
                        WorkspaceEntityRelation :: PROPERTY_ENTITY_ID
                    ),
                    new StaticConditionVariable($entityIdentifier)
                );
                $entityConditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        WorkspaceEntityRelation:: class_name(),
                        WorkspaceEntityRelation :: PROPERTY_ENTITY_TYPE
                    ),
                    new StaticConditionVariable($entityType)
                );
                $entityConditions[] = new EqualityCondition(
                    new OperationConditionVariable(
                        new PropertyConditionVariable(
                            WorkspaceEntityRelation:: class_name(),
                            WorkspaceEntityRelation :: PROPERTY_RIGHTS
                        ),
                        OperationConditionVariable :: BITWISE_AND,
                        new StaticConditionVariable($right)
                    ),
                    new StaticConditionVariable($right)
                );

                $conditions[] = new AndCondition($entityConditions);
            }
        }

        return new OrCondition($conditions);
    }

    /**
     *
     * @param integer $joinType
     *
     * @return \Chamilo\Libraries\Storage\Query\Join
     */
    private function getSharedWorkspacesJoin($joinType = Join :: TYPE_NORMAL)
    {
        return new Join(
            WorkspaceEntityRelation:: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    WorkspaceEntityRelation:: class_name(),
                    WorkspaceEntityRelation :: PROPERTY_WORKSPACE_ID
                ),
                new PropertyConditionVariable(Workspace:: class_name(), Workspace :: PROPERTY_ID)
            ),
            $joinType
        );
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findWorkspaceFavouritesByUser(
        User $user, $entities, $limit = null, $offset = null, $orderProperty = array()
    )
    {
        return DataManager:: retrieves(
            Workspace:: class_name(),
            new DataClassRetrievesParameters(
                $this->getWorkspaceFavouritesByUserCondition($user, $entities, RightsService :: RIGHT_VIEW),
                $limit,
                $offset,
                $orderProperty,
                $this->getWorkspaceFavouritesByUserJoins(),
                true
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
        return DataManager:: count(
            Workspace:: class_name(),
            new DataClassCountParameters(
                $this->getWorkspaceFavouritesByUserCondition($user, $entities, RightsService :: RIGHT_VIEW),
                $this->getWorkspaceFavouritesByUserJoins(),
                new FunctionConditionVariable(
                    FunctionConditionVariable :: DISTINCT,
                    new PropertyConditionVariable(Workspace:: class_name(), Workspace :: PROPERTY_ID)
                )
            )
        );
    }

    /**
     *
     * @param integer $joinType
     *
     * @return \Chamilo\Libraries\Storage\Query\Join
     */
    private function getFavouritesJoin($joinType = Join :: TYPE_NORMAL)
    {
        return new Join(
            WorkspaceUserFavourite:: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    WorkspaceUserFavourite:: class_name(),
                    WorkspaceUserFavourite :: PROPERTY_WORKSPACE_ID
                ),
                new PropertyConditionVariable(Workspace:: class_name(), Workspace :: PROPERTY_ID)
            ),
            $joinType
        );
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    private function getWorkspaceFavouritesByUserJoins()
    {
        $joins = new Joins();
        $joins->add($this->getSharedWorkspacesJoin(Join :: TYPE_LEFT));
        $joins->add($this->getFavouritesJoin());

        return $joins;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    private function getWorkspaceByUserCondition(User $user, $entities, $right)
    {
        $orConditions = array();

        $orConditions[] = $this->getWorkspacesByCreatorCondition($user);
        $orConditions[] = $this->getSharedWorkspacesForEntitiesWithRightCondition($entities, $right);

        return new OrCondition($orConditions);
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
        $andConditions = array();

        $andConditions[] = $this->getWorkspaceByUserCondition($user, $entities, $right);
        $andConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceUserFavourite:: class_name(),
                WorkspaceUserFavourite :: PROPERTY_USER_ID
            ),
            new StaticConditionVariable($user->getId())
        );

        return new AndCondition($andConditions);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     * @param integer $right
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findWorkspacesForUser(User $user, $entities, $right, $limit, $offset, $orderProperty = null)
    {
        return DataManager:: retrieves(
            Workspace:: class_name(),
            new DataClassRetrievesParameters(
                $this->getWorkspaceByUserCondition($user, $entities, $right),
                $limit,
                $offset,
                $orderProperty,
                new Joins(array($this->getSharedWorkspacesJoin(Join :: TYPE_LEFT)))
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
    public function countWorkspacesForUser(User $user, $entities, $right)
    {
        return DataManager:: count(
            Workspace:: class_name(),
            new DataClassCountParameters(
                $this->getWorkspaceByUserCondition($user, $entities, $right),
                new Joins(array($this->getSharedWorkspacesJoin(Join :: TYPE_LEFT)))
            )
        );
    }

}