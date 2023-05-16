<?php
namespace Chamilo\Libraries\File\Compression\ArchiveCreator;

/**
 * @package Chamilo\Libraries\File\Compression\ArchiveCreator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ArchiveFile extends ArchiveItem
{
    /**
     * @var string
     */
    protected $originalPath;

    /**
     * @return string
     */
    public function getOriginalPath()
    {
        return $this->originalPath;
    }

    /**
     * @param string $originalPath
     */
    public function setOriginalPath(string $originalPath)
    {
        $this->originalPath = $originalPath;
    }
}