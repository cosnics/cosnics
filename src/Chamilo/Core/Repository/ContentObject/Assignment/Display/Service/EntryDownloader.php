<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment
     */
    private $assignment;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     */
    public function __construct(AssignmentDataProvider $assignmentDataProvider, Assignment $assignment)
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
        $this->assignment = $assignment;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    protected function getAssignmentDataProvider()
    {
        return $this->assignmentDataProvider;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     */
    protected function setAssignmentDataProvider(AssignmentDataProvider $assignmentDataProvider)
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment
     */
    protected function getAssignment()
    {
        return $this->assignment;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     */
    protected function setAssignment(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    protected function getAssignmentName()
    {
        return $this->getAssignment()->get_title();
    }

    protected function getEntityArchiveFileName($entityType, $entityIdentifier)
    {
        $entityRenderer = $this->getAssignmentDataProvider()->getEntityRendererForEntityTypeAndId(
            $entityType,
            $entityIdentifier);

        $entityName = $entityRenderer->getEntityName();

        $archiveFileNameParts = array();
        $archiveFileNameParts[] = $this->getAssignmentName();
        $archiveFileNameParts[] = $entityName;

        return implode(' - ', $archiveFileNameParts);
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $entryIdentifier
     */
    public function downloadByEntryIdentifier(Request $request, $entryIdentifier)
    {
        return $this->downloadByEntryIdentifiers($request, array($entryIdentifier));
    }

    /**
     *
     * @param integer $entryIdentifier
     * @return string
     */
    public function compressByEntryIdentifier($entryIdentifier)
    {
        return $this->compressByEntryIdentifiers(array($entryIdentifier));
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer[] $entryIdentifier
     */
    public function downloadByEntryIdentifiers(Request $request, $entryIdentifiers)
    {
        return $this->downloadEntries($request, $this->compressByEntryIdentifiers($entryIdentifiers));
    }

    /**
     *
     * @param integer[] $entryIdentifiers
     * @return string
     */
    public function compressByEntryIdentifiers($entryIdentifiers)
    {
        $entries = $this->getAssignmentDataProvider()->findEntriesByIdentifiers($entryIdentifiers);
        $entry = $entries[0];

        return $this->compressEntries(
            $this->getEntityArchiveFileName($entry->getEntityType(), $entry->getEntityId()),
            $entries);
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $entityType
     * @param integer $entityIdentifier
     */
    public function downloadForEntityTypeAndIdentifier(Request $request, $entityType, $entityIdentifier)
    {
        return $this->downloadEntries(
            $request,
            $this->compressForEntityTypeAndIdentifier($entityType, $entityIdentifier));
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityIdentifier
     * @return string
     */
    public function compressForEntityTypeAndIdentifier($entityType, $entityIdentifier)
    {
        $entries = $this->getAssignmentDataProvider()->findEntriesByEntityTypeAndIdentifiers(
            $entityType,
            array($entityIdentifier));

        return $this->compressEntries($this->getEntityArchiveFileName($entityType, $entityIdentifier), $entries);
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     */
    public function downloadForEntityTypeAndIdentifiers(Request $request, $entityType, $entityIdentifiers)
    {
        return $this->downloadEntries(
            $request,
            $this->compressForEntityTypeAndIdentifiers($entityType, $entityIdentifiers));
    }

    /**
     *
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     * @return string
     */
    public function compressForEntityTypeAndIdentifiers($entityType, $entityIdentifiers)
    {
        $entries = $this->getAssignmentDataProvider()->findEntriesByEntityTypeAndIdentifiers(
            $entityType,
            $entityIdentifiers);

        return $this->compressEntries($this->getAssignmentName(), $entries);
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function downloadAll(Request $request)
    {
        return $this->downloadEntries($request, $this->compressAll());
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function downloadByRequest(Request $request)
    {
        $entryIdentifiers = $request->get(Manager :: PARAM_ENTRY_ID);

        if (! is_null($entryIdentifiers))
        {
            if (! is_array($entryIdentifiers))
            {
                $entryIdentifiers = array($entryIdentifiers);
            }

            return $this->downloadByEntryIdentifiers($request, $entryIdentifiers);
        }

        $entityType = $request->get(Manager :: PARAM_ENTITY_TYPE);
        $entityIdentifiers = $request->get(Manager :: PARAM_ENTITY_ID);

        if (! is_null($entityType) && ! is_null($entityIdentifiers))
        {
            if (! is_array($entityIdentifiers))
            {
                return $this->downloadForEntityTypeAndIdentifier($request, $entityType, $entityIdentifiers);
            }
            else
            {
                return $this->downloadForEntityTypeAndIdentifiers($request, $entityType, $entityIdentifiers);
            }
        }

        return $this->downloadAll($request);
    }

    /**
     *
     * @return string
     */
    public function compressAll()
    {
        $entries = $this->getAssignmentDataProvider()->findEntries();
        return $this->compressEntries($this->getAssignmentName(), $entries);
    }

    /**
     *
     * @param string $fileName
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry[] $entries
     * @return string
     */
    protected function compressEntries($fileName, $entries)
    {
        $temporaryPath = Path :: getInstance()->getTemporaryPath(__NAMESPACE__) . uniqid() . DIRECTORY_SEPARATOR;
        $archiveController = new ArchiveController($temporaryPath, $fileName);

        foreach ($entries as $entry)
        {
            $entityRenderer = $this->getAssignmentDataProvider()->getEntityRendererForEntityTypeAndId(
                $entry->getEntityType(),
                $entry->getEntityId());

            $entityName = $entityRenderer->getEntityName();
            $contentObject = $entry->getContentObject();

            $virtualTargetFolder = $entityName;
            $systemTargetFolder = $temporaryPath . DIRECTORY_SEPARATOR . $virtualTargetFolder;

            $entryName = $contentObject->get_filename();

            if (strpos($contentObject->get_filename(), $contentObject->get_title()) === false)
            {
                $entryName = $contentObject->get_title() . ' - ' . $entryName;
            }

            $entryFileName = basename(Filesystem :: create_unique_name($systemTargetFolder, $entryName));
            $virtualTargetPath = $virtualTargetFolder . DIRECTORY_SEPARATOR . $entryFileName;

            $archiveController->addPath($contentObject->get_full_path(), $virtualTargetPath);
        }

        return $archiveController->getArchivePath();
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $fileName
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry[] $entries
     */
    protected function downloadEntries(Request $request, $archivePath)
    {
        $archiveName = basename($archivePath);
        $archiveSafeName = Filesystem :: create_safe_name($archiveName);

        $response = new BinaryFileResponse($archivePath, 200, array('Content-Type' => 'application/zip'));
        $response->setContentDisposition(ResponseHeaderBag :: DISPOSITION_ATTACHMENT, $archiveName, $archiveSafeName);
        $response->prepare($request);
        $response->send();

        Filesystem :: remove($archivePath);
    }
}