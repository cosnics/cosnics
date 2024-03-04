<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\EphorusRepository;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusService extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\EphorusService
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\EphorusRepository
     */
    protected $ephorusRepository;

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\EphorusRepository $ephorusRepository
     */
    public function __construct(EphorusRepository $ephorusRepository)
    {
        parent::__construct($ephorusRepository);
    }

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
     * @param \Chamilo\Libraries\Storage\Parameters\RetrievesParameters $retrievesParameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|\Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function findAssignmentEntriesWithEphorusRequestsByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication,
        RetrievesParameters $retrievesParameters = new RetrievesParameters()
    )
    {
        return $this->ephorusRepository->findAssignmentEntriesWithRequestsByContentObjectPublication(
            $contentObjectPublication, $retrievesParameters
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