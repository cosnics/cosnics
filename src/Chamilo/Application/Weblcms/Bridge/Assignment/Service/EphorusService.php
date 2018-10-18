<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\AssignmentEphorusRepository;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\AssignmentRepository;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusService extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\EphorusService
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\EphorusRepository
     */
    protected $ephorusRepository;

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithEphorusRequestsByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null
    )
    {
        return $this->ephorusRepository->countAssignmentEntriesWithRequestsByContentObjectPublication(
            $contentObjectPublication, $condition
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|\Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function findAssignmentEntriesWithEphorusRequestsByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, RecordRetrievesParameters $recordRetrievesParameters = null
    )
    {
        return $this->ephorusRepository->findAssignmentEntriesWithRequestsByContentObjectPublication(
            $contentObjectPublication, $recordRetrievesParameters
        );
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
        return $this->ephorusRepository->findEphorusRequestsForAssignmentEntriesByContentObjectPublication(
            $contentObjectPublication, $entryIds
        );
    }
}