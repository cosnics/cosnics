<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;

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
     * @param PersonalWorkspace $personalWorkspace
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAllInPersonalWorkspace(PersonalWorkspace $personalWorkspace,
        ConditionFilterRenderer $filterConditionRenderer, $count, $offset, $orderProperty)
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getPersonalWorkspaceConditions($personalWorkspace, $filterConditionRenderer),
            $count,
            $offset,
            $orderProperty);

        return $this->findAll($parameters);
    }

    public function countAllInPersonalWorkspace(PersonalWorkspace $personalWorkspace,
        ConditionFilterRenderer $filterConditionRenderer)
    {
        $parameters = new DataClassCountParameters(
            $this->getPersonalWorkspaceConditions($personalWorkspace, $filterConditionRenderer));
        return $this->countAll($parameters);
    }

    private function getPersonalWorkspaceConditions(PersonalWorkspace $personalWorkspace,
        ConditionFilterRenderer $filterConditionRenderer)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable($personalWorkspace->getOwner()->getId()));
        $conditions[] = $this->getActiveContentObjectConditions($filterConditionRenderer);

        return new AndCondition($conditions);
    }

    /**
     *
     * @param Workspace $workspace
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAllInWorkspace(Workspace $workspace, ConditionFilterRenderer $filterConditionRenderer, $count,
        $offset, $orderProperty)
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getWorkspaceConditions($workspace, $filterConditionRenderer),
            $count,
            $offset,
            $orderProperty,
            $this->getWorkspaceJoins());

        return $this->findAll($parameters);
    }

    /**
     *
     * @param Workspace $workspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     * @return integer
     */
    public function countAllInWorkspace(Workspace $workspace, ConditionFilterRenderer $filterConditionRenderer)
    {
        $parameters = new DataClassCountParameters(
            $this->getWorkspaceConditions($workspace, $filterConditionRenderer),
            $this->getWorkspaceJoins());
        return $this->countAll($parameters);
    }

    /**
     *
     * @param Workspace $workspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    private function getWorkspaceConditions(Workspace $workspace, ConditionFilterRenderer $filterConditionRenderer)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable($workspace->getId()));
        $conditions[] = $this->getActiveContentObjectConditions($filterConditionRenderer);

        return new AndCondition($conditions);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    private function getWorkspaceJoins()
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation :: class_name(),
                WorkspaceContentObjectRelation :: PROPERTY_CONTENT_OBJECT_ID));

        $join = new Join(WorkspaceContentObjectRelation :: class_name(), $joinCondition);
        return new Joins(array($join));
    }

    /**
     *
     * @param DataClassRetrievesParameters $parameters
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAll(DataClassRetrievesParameters $parameters)
    {
        return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_objects(
            ContentObject :: class_name(),
            $parameters);
    }

    /**
     *
     * @param DataClassCountParameters $parameters
     * @return integer
     */
    public function countAll(DataClassCountParameters $parameters)
    {
        return \Chamilo\Core\Repository\Storage\DataManager :: count_content_objects(
            ContentObject :: class_name(),
            $parameters);
    }

    private function getActiveContentObjectConditions(ConditionFilterRenderer $filterConditionRenderer)
    {
        $conditions = array();

        $filterCondition = $filterConditionRenderer->render();

        if ($filterCondition instanceof Condition)
        {
            $conditions[] = $filterCondition;
        }

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_CURRENT),
                new StaticConditionVariable(ContentObject :: CURRENT_OLD)));

        $conditions[] = $this->getStateCondition(ContentObject :: STATE_NORMAL);
        $conditions[] = $this->getActiveHelperTypeConditions();

        return new AndCondition($conditions);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    private function getActiveHelperTypeConditions()
    {
        $conditions = array();
        $types = \Chamilo\Core\Repository\Storage\DataManager :: get_active_helper_types();

        foreach ($types as $type)
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
                    new StaticConditionVariable($type)));
        }

        return new AndCondition($conditions);
    }

    /**
     *
     * @param integer $state
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    private function getStateCondition($state)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_STATE),
            new StaticConditionVariable($state));
    }
}