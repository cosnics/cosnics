<?php

namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
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
class EntityRelationRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\OrCondition[]
     */
    private static $entitiesConditions = array();

    /**
     *
     * @param integer[] $entities
     * @param integer $right
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function findEntitiesWithRight($entities, $right, WorkspaceInterface $workspaceImplementation)
    {
        $entityRelationConditions = array();

        $entityRelationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class,
                WorkspaceEntityRelation::PROPERTY_WORKSPACE_ID
            ),
            new StaticConditionVariable($workspaceImplementation->getId())
        );

        $entityRelationConditions[] = new EqualityCondition(
            new OperationConditionVariable(
                new PropertyConditionVariable(
                    WorkspaceEntityRelation::class,
                    WorkspaceEntityRelation::PROPERTY_RIGHTS
                ),
                OperationConditionVariable::BITWISE_AND,
                new StaticConditionVariable($right)
            ),
            new StaticConditionVariable($right)
        );

        $entityRelationConditions[] = $this->getEntitiesCondition($entities);

        $entityRelationCondition = new AndCondition($entityRelationConditions);

        return DataManager::count(
                WorkspaceEntityRelation::class,
                new DataClassCountParameters($entityRelationCondition)
            ) > 0;
    }

    /**
     *
     * @param integer[] $entities
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    private function getEntitiesCondition($entities)
    {
        $entitiesHash = md5(serialize($entities));

        if (!isset(self::$entitiesConditions[$entitiesHash]))
        {
            $entityTypeConditions = array();

            foreach ($entities as $entityType => $entityIdentifiers)
            {
                foreach ($entityIdentifiers as $entityIdentifier)
                {

                    $entityConditions = array();

                    $entityConditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            WorkspaceEntityRelation::class,
                            WorkspaceEntityRelation::PROPERTY_ENTITY_TYPE
                        ),
                        new StaticConditionVariable($entityType)
                    );
                    $entityConditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            WorkspaceEntityRelation::class,
                            WorkspaceEntityRelation::PROPERTY_ENTITY_ID
                        ),
                        new StaticConditionVariable($entityIdentifier)
                    );

                    $entityTypeConditions[] = new AndCondition($entityConditions);
                }
            }

            self::$entitiesConditions[$entitiesHash] = new OrCondition($entityTypeConditions);
        }

        return self::$entitiesConditions[$entitiesHash];
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param integer $entityType
     * @param integer $entityIdentifier
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation
     */
    public function findEntityRelationForWorkspaceEntityTypeAndIdentifier(
        Workspace $workspace, $entityType,
        $entityIdentifier
    )
    {
        $entityConditions = array();

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class,
                WorkspaceEntityRelation::PROPERTY_WORKSPACE_ID
            ),
            new StaticConditionVariable($workspace->getId())
        );

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class,
                WorkspaceEntityRelation::PROPERTY_ENTITY_TYPE
            ),
            new StaticConditionVariable($entityType)
        );
        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class,
                WorkspaceEntityRelation::PROPERTY_ENTITY_ID
            ),
            new StaticConditionVariable($entityIdentifier)
        );

        $entityCondition = new AndCondition($entityConditions);

        return DataManager::retrieve(
            WorkspaceEntityRelation::class,
            new DataClassRetrieveParameters($entityCondition)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return WorkspaceEntityRelation[] | mixed[]
     */
    public function findEntityRelationsForWorkspace(Workspace $workspace)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class,
                WorkspaceEntityRelation::PROPERTY_WORKSPACE_ID
            ),
            new StaticConditionVariable($workspace->getId())
        );

        return DataManager::retrieves(
            WorkspaceEntityRelation::class, new DataClassRetrievesParameters($condition)
        )->as_array();
    }

    /**
     *
     * @param integer $identifier
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation
     */
    public function findEntityRelationByIdentifier($identifier)
    {
        return DataManager::retrieve_by_id(WorkspaceEntityRelation::class, $identifier);
    }
}