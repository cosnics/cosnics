<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Service\EntriesDownloader;

use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\File\Compression\ArchiveCreator\Archive;
use Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFile;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntityEntriesDownloader extends EntriesDownloader
{

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry[] $entries
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\Archive $archive
     */
    function handleAssignment(Assignment $assignment, $entries, Archive $archive)
    {
        foreach ($entries as $entry)
        {
            $file = $entry->getContentObject();

            if (!$file instanceof File)
            {
                continue;
            }

            $entityFolder = $this->getOrCreateFolderByEntity(
                $entry->getEntityType(), $entry->getEntityId(), $archive
            );

            $assignmentFolder = $this->getOrCreateFolderByAssignmentAndEntity(
                $assignment, $entry->getEntityType(), $entry->getEntityId(), $entityFolder
            );

            $archiveFile = new ArchiveFile();
            $archiveFile->setName($file->get_filename());
            $archiveFile->setOriginalPath($file->get_full_path());

            $assignmentFolder->addItem($archiveFile);
        }
    }
}
