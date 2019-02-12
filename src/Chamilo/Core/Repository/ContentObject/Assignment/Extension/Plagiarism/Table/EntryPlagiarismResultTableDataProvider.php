<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryPlagiarismResultTableDataProvider extends RecordTableDataProvider
{

    /**
     * Returns the data as a resultset
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieve_data($condition, $offset, $count, $orderProperties = array())
    {
        return $this->getEntryPlagiarismResultServiceBridge()->findEntriesWithPlagiarismResult(
            $this->getEntityType(), $condition, $offset, $count, $orderProperties
        );
    }

    /**
     * Counts the data
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function count_data($condition)
    {
        return $this->getEntryPlagiarismResultServiceBridge()->countEntriesWithPlagiarismResult(
            $this->getEntityType(), $condition
        );
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table|\Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTable
     */
    protected function getEntryPlagiarismResultTable()
    {
        return $this->get_table();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface
     */
    protected function getEntryPlagiarismResultServiceBridge()
    {
        return $this->getEntryPlagiarismResultTable()->getEntryPlagiarismResultTableParameters()
            ->getEntryPlagiarismResultServiceBridge();
    }

    /**
     * @return int
     */
    protected function getEntityType()
    {
        return $this->getEntryPlagiarismResultTable()->getEntryPlagiarismResultTableParameters()->getEntityType();
    }
}