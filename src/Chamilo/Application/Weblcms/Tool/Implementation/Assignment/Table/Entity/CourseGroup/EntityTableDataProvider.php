<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\CourseGroup;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\CourseGroup
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EntityTableDataProvider
    extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\Group\EntityTableDataProvider
{
    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::retrieve_data()
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $offset
     * @param int $count
     * @param null $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|\Chamilo\Libraries\Storage\Iterator\RecordIterator|\Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->getTable()->getAssignmentService()->findTargetCourseGroupsForContentObjectPublication(
            $this->getTable()->getContentObjectPublication(), $this->getTable()->getUserIds(), $condition, $offset, $count,
            $order_property
        );
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::count_data()
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->getTable()->getAssignmentService()->countTargetCourseGroupsForContentObjectPublication(
            $this->getTable()->getContentObjectPublication(), $this->getTable()->getUserIds(), $condition
        );
    }
}