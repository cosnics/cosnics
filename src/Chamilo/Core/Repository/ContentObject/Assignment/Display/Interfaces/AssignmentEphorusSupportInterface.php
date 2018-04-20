<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces;

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
    public function countAssignmentEntriesWithRequests(Condition $condition = null);

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findAssignmentEntriesWithRequests(RecordRetrievesParameters $recordRetrievesParameters = null);
}