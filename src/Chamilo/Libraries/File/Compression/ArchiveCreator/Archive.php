<?php
namespace Chamilo\Libraries\File\Compression\ArchiveCreator;

/**
 * @package Chamilo\Libraries\File\Compression\ArchiveCreator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Archive extends ArchiveFolder
{
    /**
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\Archive $archive
     *
     * @return \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveFolder
     */
    protected function createFolderFromArchive(Archive $archive)
    {
        $archiveFolder = new ArchiveFolder();

        $archiveFolder->setName($archive->getName());
        $archiveFolder->setArchiveItems($archive->getArchiveItems());

        return $archiveFolder;
    }

    /**
     * Merges another archive as a folder into this archive
     *
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\Archive $archive
     */
    public function mergeArchive(Archive $archive)
    {
        $archiveFolder = $this->createFolderFromArchive($archive);
        $this->addItem($archiveFolder);
    }
}