<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\DataClassParameters;
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
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithRequestsByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null
    )
    {
        return $this->countAssignmentEntriesWithRequests(
            $this->getConditionsByContentObjectPublication($contentObjectPublication, $condition)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\DataClassParameters $dataClassParameters
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function findAssignmentEntriesWithRequestsByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication,
        DataClassParameters $dataClassParameters = new DataClassParameters()
    )
    {
        $entryConditions = $this->getConditionsByContentObjectPublication(
            $contentObjectPublication, $dataClassParameters->getCondition()
        );
        $dataClassParameters->setCondition($entryConditions);

        return $this->findAssignmentEntriesWithRequests($dataClassParameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param array $entryIds
     *
     * @return mixed
     */
    public function findEphorusRequestsForAssignmentEntriesByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, array $entryIds = []
    )
    {
        return $this->findEphorusRequestsForAssignmentEntries(
            $entryIds, $this->getConditionsByContentObjectPublication($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return Condition
     */
    protected function getConditionsByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null
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

    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return Entry::class;
    }
}