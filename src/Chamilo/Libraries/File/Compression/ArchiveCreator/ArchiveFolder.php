<?php
namespace Chamilo\Libraries\File\Compression\ArchiveCreator;

/**
 * @package Chamilo\Libraries\File\Compression\ArchiveCreator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ArchiveFolder extends ArchiveItem
{
    /**
     * @var ArchiveItem[]
     */
    protected $archiveItems;

    /**
     * Archive constructor.
     */
    public function __construct()
    {
        $this->archiveItems = [];
    }

    /**
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveItem $archiveItem
     */
    public function addItem(ArchiveItem $archiveItem)
    {
        $this->archiveItems[] = $archiveItem;
    }

    /**
     * @return \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveItem[]
     */
    public function getArchiveItems()
    {
        return $this->archiveItems;
    }

    /**
     * @param \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveItem[] $archiveItems
     */
    public function setArchiveItems(array $archiveItems)
    {
        $this->archiveItems = $archiveItems;
    }
}