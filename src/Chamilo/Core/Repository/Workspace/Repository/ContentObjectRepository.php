<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;

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
    public function findAllInPersonalWorkspace(PersonalWorkspace $personalWorkspace, $count, $offset, $orderProperty)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable($personalWorkspace->getOwner()->getId()));
        $conditions[] = $this->getActiveContentObjectConditions();

        $parameters = new DataClassRetrievesParameters(new AndCondition($conditions), $count, $offset, $orderProperty);

        return $this->findAll($parameters);
    }

    /**
     *
     * @param Workspace $workspace
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findAllInWorkspace(Workspace $workspace, $count, $offset, $orderProperty)
    {
        $joinCondition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation :: class_name(),
                WorkspaceContentObjectRelation :: PROPERTY_WORKSPACE_ID));

        $join = new Join(WorkspaceContentObjectRelation :: class_name(), $joinCondition);

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
            new StaticConditionVariable($workspace->getId()));
        $conditions[] = $this->getActiveContentObjectConditions();

        $parameters = new DataClassRetrievesParameters(
            new AndCondition($conditions),
            $count,
            $offset,
            $orderProperty,
            new Joins(array($join)));

        return $this->findAll($parameters);
    }

    public function findAll(DataClassRetrievesParameters $parameters)
    {
        return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_objects(
            ContentObject :: class_name(),
            $parameters);
    }

    private function getActiveContentObjectConditions()
    {
        $conditions = array();

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