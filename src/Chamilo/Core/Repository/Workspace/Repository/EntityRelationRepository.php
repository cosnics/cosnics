<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;

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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $right
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function findUserWithRight(User $user, $right, WorkspaceInterface $workspaceImplementation)
    {
        $entityConditions = array();

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation :: class_name(),
                WorkspaceEntityRelation :: PROPERTY_WORKSPACE_ID),
            new StaticConditionVariable($workspaceImplementation->getId()));

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation :: class_name(),
                WorkspaceEntityRelation :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable(User :: class_name()));
        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation :: class_name(),
                WorkspaceEntityRelation :: PROPERTY_ENTITY_ID),
            new StaticConditionVariable($user->getId()));
        $entityConditions[] = new EqualityCondition(
            new OperationConditionVariable(
                new PropertyConditionVariable(
                    WorkspaceEntityRelation :: class_name(),
                    WorkspaceEntityRelation :: PROPERTY_RIGHTS),
                OperationConditionVariable :: BITWISE_AND,
                new StaticConditionVariable($right)),
            new StaticConditionVariable($right));

        $entityCondition = new AndCondition($entityConditions);

        return DataManager :: count(
            WorkspaceEntityRelation :: class_name(),
            new DataClassCountParameters($entityCondition)) > 0;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param integer $entityType
     * @param integer $entityIdentifier
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation
     */
    public function findEntityRelationForWorkspaceEntityTypeAndIdentifier(Workspace $workspace, $entityType,
        $entityIdentifier)
    {
        $entityConditions = array();

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation :: class_name(),
                WorkspaceEntityRelation :: PROPERTY_WORKSPACE_ID),
            new StaticConditionVariable($workspace->getId()));

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation :: class_name(),
                WorkspaceEntityRelation :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType));
        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation :: class_name(),
                WorkspaceEntityRelation :: PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entityIdentifier));

        $entityCondition = new AndCondition($entityConditions);

        return DataManager :: retrieve(
            WorkspaceEntityRelation :: class_name(),
            new DataClassRetrieveParameters($entityCondition));
    }

    /**
     * @param integer $identifier
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation
     */
    public function findEntityRelationByIdentifier($identifier)
    {
        return DataManager :: retrieve_by_id(WorkspaceEntityRelation :: class_name(), $identifier);
    }
}