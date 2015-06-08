<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
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
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function findWorkspaceByIdentifier($identifier)
    {
        return DataManager :: retrieve_by_id(Workspace :: class_name(), $identifier);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findWorkspacesByCreator(User $user, $limit, $offset, $orderProperty = array())
    {
        return DataManager :: retrieves(
            Workspace :: class_name(),
            new DataClassRetrievesParameters(
                $this->getWorkspacesByCreatorCondition($user),
                $limit,
                $offset,
                $orderProperty));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return integer
     */
    public function countWorkspacesByCreator(User $user)
    {
        return DataManager :: count(
            Workspace :: class_name(),
            new DataClassCountParameters($this->getWorkspacesByCreatorCondition($user)));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    private function getWorkspacesByCreatorCondition(User $user)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Workspace :: class_name(), Workspace :: PROPERTY_CREATOR_ID),
            new StaticConditionVariable($user->getId()));
    }

    /**
     *
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAllWorkspaces($limit, $offset, $orderProperty = array())
    {
        return DataManager :: retrieves(
            Workspace :: class_name(),
            new DataClassRetrievesParameters(null, $limit, $offset, $orderProperty));
    }

    /**
     *
     * @return integer
     */
    public function countAllWorkspaces()
    {
        return DataManager :: count(Workspace :: class_name());
    }

    /**
     *
     * @param integer[] $entities
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findSharedWorkspacesForEntities($entities, $limit, $offset, $orderProperty = array())
    {
        return DataManager :: retrieves(
            Workspace :: class_name(),
            new DataClassRetrievesParameters(
                $this->getSharedWorkspacesForEntitiesCondition($entities),
                $limit,
                $offset,
                $orderProperty,
                new Joins(array($this->getSharedWorkspacesJoin()))));
    }

    /**
     *
     * @param integer[] $entities
     * @return integer
     */
    public function countSharedWorkspacesForEntities($entities)
    {
        return DataManager :: count(
            Workspace :: class_name(),
            new DataClassCountParameters(
                $this->getSharedWorkspacesForEntitiesCondition($entities),
                new Joins(array($this->getSharedWorkspacesJoin()))));
    }

    /**
     *
     * @param integer[] $entities
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    private function getSharedWorkspacesForEntitiesCondition($entities)
    {
        $conditions = array();

        foreach ($entities as $entityType => $entityIdentifiers)
        {
            foreach ($entityIdentifiers as $entityIdentifier)
            {
                $entityConditions = array();

                $entityConditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        WorkspaceEntityRelation :: class_name(),
                        WorkspaceEntityRelation :: PROPERTY_ENTITY_ID),
                    new StaticConditionVariable($entityIdentifier));
                $entityConditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        WorkspaceEntityRelation :: class_name(),
                        WorkspaceEntityRelation :: PROPERTY_ENTITY_TYPE),
                    new StaticConditionVariable($entityType));

                $conditions[] = new AndCondition($entityConditions);
            }
        }

        return new OrCondition($conditions);
    }

    /**
     *
     * @param integer $joinType
     * @return \Chamilo\Libraries\Storage\Query\Join
     */
    private function getSharedWorkspacesJoin($joinType = Join :: TYPE_NORMAL)
    {
        return new Join(
            WorkspaceEntityRelation :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    WorkspaceEntityRelation :: class_name(),
                    WorkspaceEntityRelation :: PROPERTY_WORKSPACE_ID),
                new PropertyConditionVariable(Workspace :: class_name(), Workspace :: PROPERTY_ID)),
            $joinType);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findWorkspaceFavouritesByUser(User $user, $entities, $limit, $offset, $orderProperty = array())
    {
        return DataManager :: retrieves(
            Workspace :: class_name(),
            new DataClassRetrievesParameters(
                $this->getWorkspaceFavouritesByUserCondition($user, $entities),
                $limit,
                $offset,
                $orderProperty,
                $this->getWorkspaceFavouritesByUserJoins()));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[] $entities
     * @return integer
     */
    public function countWorkspaceFavouritesByUser(User $user, $entities)
    {
        return DataManager :: count(
            Workspace :: class_name(),
            new DataClassCountParameters(
                $this->getWorkspaceFavouritesByUserCondition($user, $entities),
                $this->getWorkspaceFavouritesByUserJoins()));
    }

    /**
     *
     * @param integer $joinType
     * @return \Chamilo\Libraries\Storage\Query\Join
     */
    private function getFavouritesJoin($joinType = Join :: TYPE_NORMAL)
    {
        return new Join(
            WorkspaceUserFavourite :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    WorkspaceUserFavourite :: class_name(),
                    WorkspaceUserFavourite :: PROPERTY_WORKSPACE_ID),
                new PropertyConditionVariable(Workspace :: class_name(), Workspace :: PROPERTY_ID)),
            $joinType);
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
     * @param User $user
     * @param integer[] $entities
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    private function getWorkspaceFavouritesByUserCondition(User $user, $entities)
    {
        $orConditions = array();

        $orConditions[] = $this->getWorkspacesByCreatorCondition($user);
        $orConditions[] = $this->getSharedWorkspacesForEntitiesCondition($entities);

        return new OrCondition($orConditions);
    }
}