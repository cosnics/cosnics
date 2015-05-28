<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Core\Rights\Entity\UserEntity;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectRelationRepository
{

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function findContentObjectInWorkspace(ContentObject $contentObject, 
        WorkspaceInterface $workspaceImplementation)
    {
        $relationConditions = array();
        
        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation :: class_name(), 
                WorkspaceContentObjectRelation :: PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($contentObject->getId()));
        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation :: class_name(), 
                WorkspaceContentObjectRelation :: PROPERTY_WORKSPACE_ID), 
            new StaticConditionVariable($workspaceImplementation->getId()));
        
        $relationCondition = new AndCondition($relationConditions);
        
        return DataManager :: count(
            WorkspaceContentObjectRelation :: class_name(), 
            new DataClassCountParameters($relationCondition)) > 0;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $right
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    public function findContentObjectForUserWithRight(User $user, $right, ContentObject $contentObject)
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation :: class_name(), 
                WorkspaceContentObjectRelation :: PROPERTY_WORKSPACE_ID), 
            new PropertyConditionVariable(
                WorkspaceEntityRelation :: class_name(), 
                WorkspaceEntityRelation :: PROPERTY_WORKSPACE_ID));
        
        $join = new Join(WorkspaceEntityRelation :: class_name(), $joinCondition);
        $joins = new Joins(array($join));
        
        $relationConditions = array();
        
        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation :: class_name(), 
                WorkspaceContentObjectRelation :: PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($contentObject->getId()));
        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation :: class_name(), 
                WorkspaceEntityRelation :: PROPERTY_ENTITY_TYPE), 
            new StaticConditionVariable(UserEntity :: ENTITY_TYPE));
        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation :: class_name(), 
                WorkspaceEntityRelation :: PROPERTY_ENTITY_ID), 
            new StaticConditionVariable($user->getId()));
        $relationConditions[] = new EqualityCondition(
            new OperationConditionVariable(
                new PropertyConditionVariable(
                    WorkspaceEntityRelation :: class_name(), 
                    WorkspaceEntityRelation :: PROPERTY_RIGHTS), 
                OperationConditionVariable :: BITWISE_AND, 
                new StaticConditionVariable($right)), 
            new StaticConditionVariable($right));
        
        $relationCondition = new AndCondition($relationConditions);
        
        return DataManager :: count(
            WorkspaceContentObjectRelation :: class_name(), 
            new DataClassCountParameters($relationCondition, $joins)) > 0;
    }
}