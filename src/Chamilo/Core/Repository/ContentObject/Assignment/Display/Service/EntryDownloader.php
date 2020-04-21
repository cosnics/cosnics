<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Domain\EntryDownloadResponse;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Compression\ArchiveCreator\Archive;
use Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveCreator;
use Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFile;
use Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFolder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
     * @var ArchiveFolder[]
     */
    protected $entityFoldersCache;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    protected $assignmentDataProvider;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\RightsService
     */
    protected $rightsService;

    /**
     * @var \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveCreator
     */
    protected $archiveCreator;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment
     */
    private $assignment;

    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    protected $user;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\RightsService $rightsService
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveCreator $archiveCreator
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     */
    public function __construct(
        AssignmentDataProvider $assignmentDataProvider, RightsService $rightsService,
        ArchiveCreator $archiveCreator, User $user, Assignment $assignment
    )
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
        $this->rightsService = $rightsService;
        $this->archiveCreator = $archiveCreator;
        $this->assignment = $assignment;
        $this->user = $user;
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
        $safeTitle = stripslashes(//converts two backslashes to one
            stripslashes(//removes single backslash
                str_replace('/','',$this->getAssignment()->get_title()) //remove forward slash
            )
        );

        return $safeTitle;
    }

    protected function getEntityArchiveFileName($entityType, $entityIdentifier)
    {
        $entityName = $this->getAssignmentDataProvider()->renderEntityNameByEntityTypeAndEntityId(
            $entityType,
            $entityIdentifier
        );

        $archiveFileNameParts = array();
        $archiveFileNameParts[] = $this->getAssignmentName();
        $archiveFileNameParts[] = $entityName;

        return implode(' - ', $archiveFileNameParts);
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param integer $entryIdentifier
     */
    public function downloadByEntryIdentifier(ChamiloRequest $request, $entryIdentifier)
    {
        $this->downloadByEntryIdentifiers($request, array($entryIdentifier));
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return string
     */
    public function compressByEntryIdentifier($entryIdentifier)
    {
        return $this->compressByEntryIdentifiers(array($entryIdentifier));
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param integer[] $entryIdentifier
     */
    public function downloadByEntryIdentifiers(ChamiloRequest $request, $entryIdentifiers)
    {
        $this->downloadEntries($request, $this->compressByEntryIdentifiers($entryIdentifiers));
    }

    /**
     *
     * @param integer[] $entryIdentifiers
     *
     * @return string
     */
    public function compressByEntryIdentifiers($entryIdentifiers)
    {
        $entries = $this->getAssignmentDataProvider()->findEntriesByIdentifiers($entryIdentifiers);
        $entry = $entries[0];

        return $this->compressEntries(
            $this->getEntityArchiveFileName($entry->getEntityType(), $entry->getEntityId()),
            $entries
        );
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param integer $entityType
     * @param integer $entityIdentifier
     */
    public function downloadForEntityTypeAndIdentifier(ChamiloRequest $request, $entityType, $entityIdentifier)
    {
        $this->downloadEntries(
            $request,
            $this->compressForEntityTypeAndIdentifier($entityType, $entityIdentifier)
        );
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityIdentifier
     *
     * @return string
     */
    public function compressForEntityTypeAndIdentifier($entityType, $entityIdentifier)
    {
        $entries = $this->getAssignmentDataProvider()->findEntriesByEntityTypeAndIdentifiers(
            $entityType,
            array($entityIdentifier)
        );

        return $this->compressEntries($this->getEntityArchiveFileName($entityType, $entityIdentifier), $entries);
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     */
    public function downloadForEntityTypeAndIdentifiers(ChamiloRequest $request, $entityType, $entityIdentifiers)
    {
        $this->downloadEntries(
            $request,
            $this->compressForEntityTypeAndIdentifiers($entityType, $entityIdentifiers)
        );
    }

    /**
     *
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return string
     */
    public function compressForEntityTypeAndIdentifiers($entityType, $entityIdentifiers)
    {
        $entries = $this->getAssignmentDataProvider()->findEntriesByEntityTypeAndIdentifiers(
            $entityType,
            $entityIdentifiers
        );

        return $this->compressEntries($this->getAssignmentName(), $entries);
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function downloadAll(ChamiloRequest $request)
    {
        $this->downloadEntries($request, $this->compressAll());
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function downloadByRequest(ChamiloRequest $request)
    {
        $entityType = $request->get(Manager::PARAM_ENTITY_TYPE);
        $entityIdentifiers = $request->get(Manager::PARAM_ENTITY_ID);

        $entryIdentifiers = $request->get(Manager::PARAM_ENTRY_ID);

        if (!is_null($entryIdentifiers))
        {
            if (!is_array($entryIdentifiers))
            {
                $entryIdentifiers = array($entryIdentifiers);
            }

            if (!$this->rightsService->canUserDownloadEntriesFromEntity(
                $this->user, $this->assignment, $entityType, $entityIdentifiers
            ))
            {
                throw new NotAllowedException();
            }

            $this->downloadByEntryIdentifiers($request, $entryIdentifiers);

            return;
        }

        if (!is_null($entityType) && !is_null($entityIdentifiers))
        {
            if (!is_array($entityIdentifiers))
            {
                if (!$this->rightsService->canUserDownloadEntriesFromEntity(
                    $this->user, $this->assignment, $entityType, $entityIdentifiers
                ))
                {
                    throw new NotAllowedException();
                }

                $this->downloadForEntityTypeAndIdentifier($request, $entityType, $entityIdentifiers);

                return;
            }
            else
            {
                if (!$this->rightsService->canUserDownloadAllEntries($this->user, $this->assignment))
                {
                    throw new NotAllowedException();
                }

                $this->downloadForEntityTypeAndIdentifiers($request, $entityType, $entityIdentifiers);

                return;
            }
        }

        if (!$this->rightsService->canUserDownloadAllEntries($this->user, $this->assignment))
        {
            throw new NotAllowedException();
        }

        $this->downloadAll($request);
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[] $entries
     *
     * @return BinaryFileResponse
     */
    protected function compressEntries($fileName, $entries)
    {
        if (empty($entries))
        {
            return null;
        }

        if (count($entries) == 1)
        {
            $file = $entries[0]->getContentObject();
            if (!$file instanceof File)
            {
                return null;
            }

            return $this->createEntryDownloadResponse(
                $file->get_full_path(), $file->get_filename(), $file->get_mime_type(), false
            );
        }

        $archive = new Archive();
        $archive->setName($fileName);

        foreach ($entries as $entry)
        {
            $contentObject = $entry->getContentObject();
            if (!$contentObject instanceof File)
            {
                continue;
            }

            try
            {
                $entityName = $this->getAssignmentDataProvider()->renderEntityNameByEntityTypeAndEntityId(
                    $entry->getEntityType(), $entry->getEntityId()
                );
            }
            catch(Exception $ex)
            {
                continue;
            }

//            $entityFolder = $this->getOrCreateFolderByEntity(
//                $entry->getEntityType(), $entry->getEntityId(), $archive
//            );
//
//            if(!$entityFolder)
//            {
//                continue;
//            }

            $fileName = Filesystem::create_safe_name(
                $entityName . '_' . $contentObject->get_filename()
            );

            $archiveFile = new ArchiveFile();
            $archiveFile->setName($fileName);
            $archiveFile->setOriginalPath($contentObject->get_full_path());

            $archive->addItem($archiveFile);
        }

        $archivePath = $this->archiveCreator->createArchive($archive);

        return $this->createEntryDownloadResponse($archivePath, $archive->getName() . '.zip');
    }

    /**
     * @param string $downloadPath
     * @param string $filename
     * @param string $contentType
     * @param bool $removeAfterDownload
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Domain\EntryDownloadResponse
     */
    protected function createEntryDownloadResponse(
        $downloadPath, $filename, $contentType = 'application/zip', $removeAfterDownload = true
    )
    {
        $response = new EntryDownloadResponse($downloadPath, 200, array('Content-Type' => $contentType));

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename,
            Filesystem::create_safe_name($filename)
        );

        $response->setRemoveFileAfterDownload($removeAfterDownload);

        return $response;
    }

    /**
     * @param int $entityType
     * @param int $entityId
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFolder $parentFolder
     *
     * @return ArchiveFolder
     */
    protected function getOrCreateFolderByEntity($entityType, $entityId, ArchiveFolder $parentFolder)
    {
        $cacheKey = md5($entityType . '-' . $entityId);
        if (!array_key_exists($cacheKey, $this->entityFoldersCache))
        {
            $folder = new ArchiveFolder();

            try
            {
                $entityName = $this->getAssignmentDataProvider()->renderEntityNameByEntityTypeAndEntityId(
                    $entityType, $entityId
                );

                $folder->setName($entityName);
                $parentFolder->addItem($folder);

                $this->entityFoldersCache[$cacheKey] = $folder;
            }
            catch(Exception $ex)
            {
                $this->entityFoldersCache[$cacheKey] = null;
            }
        }

        return $this->entityFoldersCache[$cacheKey];
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Domain\EntryDownloadResponse $downloadResponse
     */
    protected function downloadEntries(ChamiloRequest $request, EntryDownloadResponse $downloadResponse = null)
    {
        if (empty($downloadResponse))
        {
            throw new RuntimeException('No downloadable entries found');
        }

        $downloadResponse->prepare($request);
        $downloadResponse->send();

        if ($downloadResponse->removeFileAfterDownload())
        {
            $this->archiveCreator->removeArchiveAfterDownload($downloadResponse);
        }
    }
}