<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EphorusServiceBridgeInterface;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusServiceBridge implements EphorusServiceBridgeInterface
{

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithEphorusRequests(Condition $condition = null)
    {
        // TODO: Implement countAssignmentEntriesWithEphorusRequests() method.
    }

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findAssignmentEntriesWithEphorusRequests(RecordRetrievesParameters $recordRetrievesParameters = null
    )
    {
        // TODO: Implement findAssignmentEntriesWithEphorusRequests() method.
    }

    /**
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntries(array $entryIds = [])
    {
        // TODO: Implement findEphorusRequestsForAssignmentEntries() method.
    }

    /**
     * @return bool
     */
    public function isEphorusEnabled()
    {
        // TODO: Implement isEphorusEnabled() method.
    }
}