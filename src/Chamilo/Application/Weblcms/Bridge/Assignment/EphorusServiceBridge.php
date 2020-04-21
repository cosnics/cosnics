<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\EphorusService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EphorusServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use RuntimeException;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusServiceBridge implements EphorusServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\EphorusService
     */
    protected $ephorusService;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var bool
     */
    protected $ephorusEnabled;

    /**
     * AssignmentDataProvider constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\EphorusService $ephorusService
     */
    public function __construct(EphorusService $ephorusService)
    {
        $this->ephorusService = $ephorusService;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        if (!$contentObjectPublication->getContentObject() instanceof Assignment)
        {
            throw new RuntimeException(
                'The given content object publication does not reference a valid assignment and should not be used'
            );
        }

        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @param bool $ephorusEnabled
     */
    public function setEphorusEnabled($ephorusEnabled = true)
    {
        $this->ephorusEnabled = $ephorusEnabled;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithEphorusRequests(Condition $condition = null)
    {
        return $this->ephorusService->countAssignmentEntriesWithEphorusRequestsByContentObjectPublication(
            $this->contentObjectPublication, $condition
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findAssignmentEntriesWithEphorusRequests(RecordRetrievesParameters $recordRetrievesParameters = null
    )
    {
        return $this->ephorusService->findAssignmentEntriesWithEphorusRequestsByContentObjectPublication(
            $this->contentObjectPublication, $recordRetrievesParameters
        );
    }

    /**
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntries(array $entryIds = [])
    {
        return $this->ephorusService->findEphorusRequestsForAssignmentEntriesByContentObjectPublication(
            $this->contentObjectPublication, $entryIds
        );
    }

    /**
     * @return bool
     */
    public function isEphorusEnabled()
    {
        return $this->ephorusEnabled;
    }
}