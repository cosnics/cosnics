<?php
namespace Chamilo\Libraries\Filesystem\Architecture\Domain\ArchiveCreator;

/**
 * @package Chamilo\Libraries\Filesystem\Architecture\Domain\ArchiveCreator
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Archive extends ArchiveFolder
{
    protected function createFolderFromArchive(Archive $archive): ArchiveFolder
    {
        $archiveFolder = new ArchiveFolder();

        $archiveFolder->setName($archive->getName());
        $archiveFolder->setArchiveItems($archive->getArchiveItems());

        return $archiveFolder;
    }

    public function mergeArchive(Archive $archive): static
    {
        $archiveFolder = $this->createFolderFromArchive($archive);
        $this->addItem($archiveFolder);

        return $this;
    }
}