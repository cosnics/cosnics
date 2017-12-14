<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\Group;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\Group
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EntryTableDataProvider
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
        $learningPathAssignmentService = $this->getTable()->getAssignmentService();

        return $learningPathAssignmentService->findEntriesForContentObjectPublicationEntityTypeAndId(
            $this->getTable()->getContentObjectPublication(),
            $this->getEntityType(),
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
        $learningPathAssignmentService = $this->getTable()->getAssignmentService();

        return $learningPathAssignmentService->countEntriesForContentObjectPublicationEntityTypeAndId(
            $this->getTable()->getContentObjectPublication(),
            $this->getEntityType(),
            $this->getTable()->getEntityId(),
            $condition
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\Group\EntryTable | \Chamilo\Libraries\Format\Table\Table
     */
    public function getTable()
    {
        return $this->get_table();
    }

    /**
     * @return int
     */
    abstract function getEntityType();
}