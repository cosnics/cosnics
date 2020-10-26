<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity;



/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTableDataProvider
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableDataProvider
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
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->getTable()->getLearningPathAssignmentService()->findTargetUsersForTreeNodeData(
            $this->getTable()->getContentObjectPublication(), $this->getTable()->getTreeNodeData(), $this->getTable()->getUserIds(), $condition, $offset, $count,
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
        return $this->getTable()->getLearningPathAssignmentService()->countTargetUsersForTreeNodeData(
            $this->getTable()->getContentObjectPublication(),  $this->getTable()->getTreeNodeData(), $this->getTable()->getUserIds(), $condition
        );
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table | \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity\EntityTable
     */
    protected function getTable()
    {
        return $this->get_table();
    }
}