<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entry;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableDataProvider
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableDataProvider
{
    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::retrieve_data()
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $offset
     * @param int $count
     * @param null $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|\Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $orderProperty = null)
    {
        $learningPathAssignmentService = $this->getTable()->getLearningPathAssignmentService();

        return $learningPathAssignmentService->findEntriesForTreeNodeDataEntityTypeAndId(
            $this->getTable()->getTreeNodeData(),
            Entry::ENTITY_TYPE_USER,
            $this->getTable()->getEntityId(),
            $condition,
            $offset,
            $count,
            $orderProperty
        );
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableDataProvider::count_data()
     */
    public function count_data($condition)
    {
        $learningPathAssignmentService = $this->getTable()->getLearningPathAssignmentService();

        return $learningPathAssignmentService->countEntriesForTreeNodeDataEntityTypeAndId(
            $this->getTable()->getTreeNodeData(),
            Entry::ENTITY_TYPE_USER,
            $this->getTable()->getEntityId(),
            $condition
        );
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entry\EntryTable|\Chamilo\Libraries\Format\Table\Table
     */
    public function getTable()
    {
        return $this->get_table();
    }
}