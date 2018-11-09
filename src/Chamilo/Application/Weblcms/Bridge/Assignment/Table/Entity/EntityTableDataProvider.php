<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity;

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
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|\Chamilo\Libraries\Storage\Iterator\RecordIterator|\Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->getTable()->getEntityService()->retrieveEntities(
            $this->getTable()->getContentObjectPublication(), $condition, $offset, $count, $order_property
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
        return $this->getTable()->getEntityService()->countEntities(
            $this->getTable()->getContentObjectPublication(), $condition
        );
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table | \Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity\User\EntityTable
     */
    protected function getTable()
    {
        return $this->get_table();
    }
}