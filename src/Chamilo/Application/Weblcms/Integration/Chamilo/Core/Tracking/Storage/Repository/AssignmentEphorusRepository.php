<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentEphorusRepository extends
    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Storage\Repository\AssignmentEphorusRepository
{
    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findAssignmentEntriesWithRequestsByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, RecordRetrievesParameters $recordRetrievesParameters
    )
    {
        $entryConditions = $this->getConditionsByContentObjectPublication(
            $contentObjectPublication, $recordRetrievesParameters->getCondition()
        );
        $recordRetrievesParameters->setCondition($entryConditions);

        return $this->findAssignmentEntriesWithRequests($recordRetrievesParameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithRequestsByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, Condition $condition
    )
    {
        return $this->countAssignmentEntriesWithRequests(
            $this->getConditionsByContentObjectPublication($contentObjectPublication, $condition)
        );
    }

    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return Entry::class;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return Condition
     */
    private function getConditionsByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, Condition $condition
    )
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Entry::class, Entry::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID),
            new StaticConditionVariable($contentObjectPublication->getId())
        );

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
    }
}