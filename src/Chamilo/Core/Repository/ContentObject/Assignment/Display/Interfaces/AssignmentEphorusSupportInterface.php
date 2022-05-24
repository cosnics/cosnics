<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Interface AssignmentEphorusSupportInterface
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces
 */
interface AssignmentEphorusSupportInterface
{
    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithEphorusRequests(Condition $condition = null);

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function findAssignmentEntriesWithEphorusRequests(RecordRetrievesParameters $recordRetrievesParameters = null);

    /**
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntries(array $entryIds = []);

    /**
     * @return bool
     */
    public function isEphorusEnabled();
}