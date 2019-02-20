<?php

namespace Chamilo\Application\Plagiarism\Table;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 * @package Chamilo\Application\Plagiarism\Table
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismResultTableDataProvider extends RecordTableDataProvider
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
        return $this->getPlagiarismResultService()->findPlagiarismResults(
            $this->getCourse(), $this->getFilterParameters()
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
        return $this->getPlagiarismResultService()->countPlagiarismResults(
            $this->getCourse(), $this->getFilterParameters()
        );
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table|\Chamilo\Application\Plagiarism\Table\PlagiarismResultTable
     */
    protected function getPlagiarismResultTable()
    {
        return $this->get_table();
    }

    /**
     * @return \Chamilo\Application\Plagiarism\Table\PlagiarismResultTableParameters
     */
    protected function getPlagiarismResultTableParameters()
    {
        return $this->getPlagiarismResultTable()->getPlagiarismResultTableParameters();
    }

    /**
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    protected function getCourse()
    {
        return $this->getPlagiarismResultTableParameters()->getCourse();
    }

    /**
     * @return \Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismResultService
     */
    protected function getPlagiarismResultService()
    {
        return $this->getPlagiarismResultTableParameters()->getContentObjectPlagiarismResultService();
    }
}