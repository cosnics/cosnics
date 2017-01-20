<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectRepository
{

    /**
     *
     * @param string $contentObjectClassName
     * @param WorkspaceInterface $personalWorkspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     * @param int $count
     * @param int $offset
     * @param OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAllInPersonalWorkspace($contentObjectClassName, WorkspaceInterface $personalWorkspace,
        ConditionFilterRenderer $filterConditionRenderer, $count, $offset, $orderProperty)
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getPersonalWorkspaceConditions($personalWorkspace, $filterConditionRenderer),
            $count,
            $offset,
            $orderProperty);

        return $this->findAll($contentObjectClassName, $parameters);
    }

    /**
     *
     * @param string $contentObjectClassName
     * @param WorkspaceInterface $personalWorkspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     *
     * @return int
     */
    public function countAllInPersonalWorkspace($contentObjectClassName, WorkspaceInterface $personalWorkspace,
        ConditionFilterRenderer $filterConditionRenderer)
    {
        $parameters = new DataClassCountParameters(
            $this->getPersonalWorkspaceConditions($personalWorkspace, $filterConditionRenderer));

        return $this->countAll($contentObjectClassName, $parameters);
    }

    /**
     *
     * @param WorkspaceInterface $personalWorkspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     *
     * @return AndCondition
     */
    protected function getPersonalWorkspaceConditions(WorkspaceInterface $personalWorkspace,
        ConditionFilterRenderer $filterConditionRenderer)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($personalWorkspace->getId()));
        $conditions[] = $this->getActiveContentObjectConditions($filterConditionRenderer);

        return new AndCondition($conditions);
    }

    /**
     *
     * @param string $contentObjectClassName
     * @param WorkspaceInterface $workspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     * @param int $count
     * @param int $offset
     * @param OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAllInWorkspace($contentObjectClassName, WorkspaceInterface $workspace,
        ConditionFilterRenderer $filterConditionRenderer, $count, $offset, $orderProperty)
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getWorkspaceConditions($workspace, $filterConditionRenderer),
            $count,
            $offset,
            $orderProperty,
            $this->getWorkspaceJoins());

        return $this->findAll($contentObjectClassName, $parameters);
    }

    /**
     *
     * @param string $contentObjectClassName
     * @param WorkspaceInterface $workspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     *
     * @return int
     */
    public function countAllInWorkspace($contentObjectClassName, WorkspaceInterface $workspace,
        ConditionFilterRenderer $filterConditionRenderer)
    {
        $parameters = new DataClassCountParameters(
            $this->getWorkspaceConditions($workspace, $filterConditionRenderer),
            $this->getWorkspaceJoins());

        return $this->countAll($contentObjectClassName, $parameters);
    }

    /**
     *
     * @param WorkspaceInterface $workspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getWorkspaceConditions(WorkspaceInterface $workspace,
        ConditionFilterRenderer $filterConditionRenderer)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class_name(),
                WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID),
            new StaticConditionVariable($workspace->getId()));
        $conditions[] = $this->getActiveContentObjectConditions($filterConditionRenderer);

        return new AndCondition($conditions);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    protected function getWorkspaceJoins()
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OBJECT_NUMBER),
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class_name(),
                WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID));

        $join = new Join(WorkspaceContentObjectRelation::class_name(), $joinCondition);

        return new Joins(array($join));
    }

    /**
     *
     * @param $contentObjectClassName
     * @param DataClassRetrievesParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAll($contentObjectClassName, DataClassRetrievesParameters $parameters)
    {
        return \Chamilo\Core\Repository\Storage\DataManager::retrieves($contentObjectClassName, $parameters);
    }

    /**
     *
     * @param $contentObjectClassName
     * @param DataClassCountParameters $parameters
     *
     * @return int
     */
    public function countAll($contentObjectClassName, DataClassCountParameters $parameters)
    {
        return \Chamilo\Core\Repository\Storage\DataManager::count($contentObjectClassName, $parameters);
    }

    /**
     * Finds a content object by a given id
     *
     * @param int $contentObjectId
     *
     * @return ContentObject
     */
    public function findById($contentObjectId)
    {
        return \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $contentObjectId);
    }

    protected function getActiveContentObjectConditions(ConditionFilterRenderer $filterConditionRenderer)
    {
        $conditions = array();

        $filterCondition = $filterConditionRenderer->render();

        if ($filterCondition instanceof Condition)
        {
            $conditions[] = $filterCondition;
        }

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_CURRENT),
                new StaticConditionVariable(ContentObject::CURRENT_OLD)));

        $conditions[] = $this->getStateCondition(ContentObject::STATE_NORMAL);
        $conditions[] = $this->getActiveHelperTypeConditions();

        return new AndCondition($conditions);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getActiveHelperTypeConditions()
    {
        $conditions = array();
        $types = \Chamilo\Core\Repository\Storage\DataManager::get_active_helper_types();

        foreach ($types as $type)
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TYPE),
                    new StaticConditionVariable($type)));
        }

        return new AndCondition($conditions);
    }

    /**
     *
     * @param integer $state
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    protected function getStateCondition($state)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_STATE),
            new StaticConditionVariable($state));
    }
}