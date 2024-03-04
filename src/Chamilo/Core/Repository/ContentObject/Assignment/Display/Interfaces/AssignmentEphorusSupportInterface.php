<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
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
     * @param \Chamilo\Libraries\Storage\Parameters\RetrievesParameters $retrievesParameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAssignmentEntriesWithEphorusRequests(
        RetrievesParameters $retrievesParameters = new RetrievesParameters()
    );

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