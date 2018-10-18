<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntriesDownloader;

use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\File\Compression\ArchiveCreator\Archive;
use Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFile;
use Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFolder;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DefaultEntriesDownloader extends EntriesDownloader
{

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[] $entries
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\Archive $archive
     */
    function handleAssignment(Assignment $assignment, $entries, Archive $archive)
    {
        $assignmentFolder = new ArchiveFolder();
        $assignmentFolder->setName($assignment->get_title());
        $archive->addItem($assignmentFolder);

        $this->entityFoldersCache = [];

        foreach ($entries as $entry)
        {
            $file = $entry->getContentObject();

            if (!$file instanceof File)
            {
                return;
            }

            $entityFolder = $this->getOrCreateFolderByEntity(
                $entry->getEntityType(), $entry->getEntityId(), $assignmentFolder
            );

            $archiveFile = new ArchiveFile();
            $archiveFile->setName($file->get_filename());
            $archiveFile->setOriginalPath($file->get_full_path());

            $entityFolder->addItem($archiveFile);
        }
    }
}