<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryDownloader
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    private $assignmentDataProvider;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     */
    public function __construct(AssignmentDataProvider $assignmentDataProvider)
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    public function getAssignmentDataProvider()
    {
        return $this->assignmentDataProvider;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     */
    public function setAssignmentDataProvider(AssignmentDataProvider $assignmentDataProvider)
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
    }

    /**
     *
     * @param integer $entryIdentifier
     */
    public function downloadByEntryIdentifier($entryIdentifier)
    {
        $entry = $this->getAssignmentDataProvider()->findEntryByIdentifier($entryIdentifier);
        return $this->compressAndDownloadEntries(array($entry));
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityIdentifier
     */
    public function downloadForEntityTypeAndIdentifier($entityType, $entityIdentifier)
    {
        $entries = $this->getAssignmentDataProvider()->findEntriesByEntityTypeAndIdentifier(
            $entityType,
            $entityIdentifier);
    }

    public function downloadAll()
    {
        $entries = $this->getAssignmentDataProvider()->findEntries();
    }

    public function compressAndDownloadEntries(ArrayResultSet $entries)
    {

    }
}