<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultTableDataProvider extends RecordTableDataProvider
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
     * @throws \Exception
     */
    public function retrieve_data($condition, $offset, $count, $orderProperties = array())
    {
        $filterParameters = $this->getFilterParameters();

        return $this->getExternalToolResultService()->getResultsWithUsers(
            $this->getContentObjectPublication(), $filterParameters
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
        $filterParameters = $this->getFilterParameters();

        return $this->getExternalToolResultService()->countResultsWithUsers(
            $this->getContentObjectPublication(), $filterParameters
        );
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table|\Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result\ResultTable
     */
    protected function getResultTable()
    {
        return $this->get_table();
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result\ResultTableParameters
     */
    protected function getResultTableParameters()
    {
        return $this->getResultTable()->getResultTableParameters();
    }

    /**
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\Service\ExternalToolResultService
     */
    protected function getExternalToolResultService()
    {
        return $this->getResultTableParameters()->getExternalToolResultService();
    }

    /**
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected function getContentObjectPublication()
    {
        return $this->getResultTableParameters()->getContentObjectPublication();
    }
}