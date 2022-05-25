<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use InvalidArgumentException;

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
        $relationConditions = [];

        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class,
                WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($contentObject->get_object_number()));
        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class,
                WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID),
            new StaticConditionVariable($workspaceImplementation->getId()));

        $relationCondition = new AndCondition($relationConditions);

        return DataManager::count(
            WorkspaceContentObjectRelation::class,
            new DataClassCountParameters($relationCondition)) > 0;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findContentObjectRelationForWorkspaceAndContentObject(Workspace $workspace,
        ContentObject $contentObject)
    {
        $relationConditions = [];

        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class,
                WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($contentObject->get_object_number()));

        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class,
                WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID),
            new StaticConditionVariable($workspace->getId()));

        $relationCondition = new AndCondition($relationConditions);

        return DataManager::retrieve(
            WorkspaceContentObjectRelation::class,
            new DataClassRetrieveParameters($relationCondition));
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findContentObjectRelationsForContentObject(ContentObject $contentObject)
    {
        return $this->findContentObjectRelationsForContentObjectById($contentObject->getId());
    }

    /**
     *
     * @param int $contentObjectId
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    // TODO: Where is this used? A content object number should be passed !
    public function findContentObjectRelationsForContentObjectById($contentObjectId)
    {
        if (empty($contentObjectId))
        {
            throw new InvalidArgumentException('The given content object id can not be empty');
        }

        $relationConditions = [];

        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class,
                WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($contentObjectId));

        $relationCondition = new AndCondition($relationConditions);

        return DataManager::retrieves(
            WorkspaceContentObjectRelation::class,
            new DataClassRetrievesParameters($relationCondition));
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @return integer[]
     */
    public function findWorkspaceIdentifiersForContentObject(ContentObject $contentObject)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class,
                WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($contentObject->get_object_number()));

        return DataManager::distinct(
            WorkspaceContentObjectRelation::class,
            new DataClassDistinctParameters(
                $condition,
                new DataClassProperties(
                    array(
                        new PropertyConditionVariable(
                            WorkspaceContentObjectRelation::class,
                            WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID)))));
    }
}