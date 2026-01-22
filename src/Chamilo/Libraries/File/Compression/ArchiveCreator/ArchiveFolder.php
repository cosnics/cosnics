<?php
namespace Chamilo\Libraries\File\Compression\ArchiveCreator;

/**
 * @package Chamilo\Libraries\File\Compression\ArchiveCreator
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ArchiveFolder extends ArchiveItem
{
    /**
     * @var ArchiveItem[]
     */
    protected array $archiveItems;

    public function __construct()
    {
        $this->archiveItems = [];
    }

    public function addItem(ArchiveItem $archiveItem): static
    {
        $this->archiveItems[] = $archiveItem;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveItem[]
     */
    public function getArchiveItems(): array
    {
        return $this->archiveItems;
    }

    /**
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveItem[] $archiveItems
     */
    public function setArchiveItems(array $archiveItems): static
    {
        $this->archiveItems = $archiveItems;

        return $this;
    }
}