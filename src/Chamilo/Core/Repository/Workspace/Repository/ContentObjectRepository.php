<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\StorageParameters;

/**
 * @package Chamilo\Core\Repository\Workspace\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectRepository
{
    public function copyIncludesFromContentObject(ContentObject $sourceContentObject, ContentObject $targetContentObject
    )
    {
        $includedObjects = $sourceContentObject->get_includes();
        foreach ($includedObjects as $includedObject)
        {
            $targetContentObject->include_content_object($includedObject->getId());
        }
    }

    /**
     * @param $contentObjectClassName
     * @param StorageParameters $parameters
     *
     * @return int
     */
    public function countAll($contentObjectClassName, StorageParameters $parameters)
    {
        return DataManager::count($contentObjectClassName, $parameters);
    }

    /**
     * @param string $contentObjectClassName
     * @param ConditionFilterRenderer $filterConditionRenderer
     *
     * @return int
     */
    public function countAllInWorkspace(
        $contentObjectClassName, Workspace $workspace, ConditionFilterRenderer $filterConditionRenderer
    )
    {
        $parameters = new StorageParameters(
            condition: $this->getWorkspaceConditions($workspace, $filterConditionRenderer),
            joins: $this->getWorkspaceJoins()
        );

        return $this->countAll($contentObjectClassName, $parameters);
    }

    /**
     * Calls the create function from the content object
     *
     * @param ContentObject $contentObject
     *
     * @return bool
     */
    public function create(ContentObject $contentObject)
    {
        return $contentObject->create();
    }

    /**
     * Calls the delete function from the content object
     *
     * @param ContentObject $contentObject
     *
     * @return bool
     */
    public function delete(ContentObject $contentObject)
    {
        return $contentObject->delete();
    }

    /**
     * @param $contentObjectClassName
     * @param StorageParameters $parameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject>
     */
    public function findAll($contentObjectClassName, StorageParameters $parameters = new StorageParameters())
    {
        return DataManager::retrieves($contentObjectClassName, $parameters);
    }

    /**
     * @param string $contentObjectClassName
     * @param ConditionFilterRenderer $filterConditionRenderer
     * @param int $count
     * @param int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject>
     */
    public function findAllInWorkspace(
        $contentObjectClassName, Workspace $workspace, ConditionFilterRenderer $filterConditionRenderer, $count,
        $offset, OrderBy $orderBy = new OrderBy()
    )
    {
        $parameters = new StorageParameters(
            condition: $this->getWorkspaceConditions($workspace, $filterConditionRenderer),
            joins: $this->getWorkspaceJoins(), orderBy: $orderBy, count: $count, offset: $offset
        );

        return $this->findAll($contentObjectClassName, $parameters);
    }

    /**
     * Finds a content object by a given id
     *
     * @param int $contentObjectId
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function findById($contentObjectId)
    {
        return DataManager::retrieve_by_id(
            ContentObject::class, $contentObjectId
        );
    }

    protected function getActiveContentObjectConditions(ConditionFilterRenderer $filterConditionRenderer)
    {
        $conditions = [];

        $filterCondition = $filterConditionRenderer->render();

        if ($filterCondition instanceof Condition)
        {
            $conditions[] = $filterCondition;
        }

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_CURRENT),
                new StaticConditionVariable(ContentObject::CURRENT_OLD)
            )
        );

        $conditions[] = $this->getStateCondition(ContentObject::STATE_NORMAL);
        $conditions[] = $this->getActiveHelperTypeConditions();

        return new AndCondition($conditions);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getActiveHelperTypeConditions()
    {
        $conditions = [];
        $types = DataManager::get_active_helper_types();

        foreach ($types as $type)
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
                    new StaticConditionVariable($type)
                )
            );
        }

        return new AndCondition($conditions);
    }

    /**
     * @param int $state
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    protected function getStateCondition($state)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
            new StaticConditionVariable($state)
        );
    }

    /**
     * @param ConditionFilterRenderer $filterConditionRenderer
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getWorkspaceConditions(
        Workspace $workspace, ConditionFilterRenderer $filterConditionRenderer
    )
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID
            ), new StaticConditionVariable($workspace->getId())
        );
        $conditions[] = $this->getActiveContentObjectConditions($filterConditionRenderer);

        return new AndCondition($conditions);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    protected function getWorkspaceJoins()
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OBJECT_NUMBER),
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID
            )
        );

        $join = new Join(WorkspaceContentObjectRelation::class, $joinCondition);

        return new Joins([$join]);
    }

    /**
     * Calls the update function from the content object
     *
     * @param ContentObject $contentObject
     *
     * @return bool
     */
    public function update(ContentObject $contentObject)
    {
        return $contentObject->update();
    }
}