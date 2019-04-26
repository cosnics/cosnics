<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Table;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

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
        $orderProperty = $orderProperties[0];
        $conditionVariable = $orderProperty->getConditionVariable();
        if ($conditionVariable instanceof PropertyConditionVariable && $conditionVariable->get_class() == User::class &&
            $conditionVariable->get_property() == User::PROPERTY_LASTNAME)
        {
            $orderProperties[] = new OrderBy(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));
        }

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
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Table\PlagiarismResultTable|\Chamilo\Libraries\Format\Table\Table
     */
    protected function getPlagiarismResultTable()
    {
        return $this->get_table();
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Table\PlagiarismResultTableParameters
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
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Service\ContentObjectPlagiarismResultService
     */
    protected function getPlagiarismResultService()
    {
        return $this->getPlagiarismResultTableParameters()->getContentObjectPlagiarismResultService();
    }
}