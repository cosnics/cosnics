<?php
namespace Chamilo\Libraries\File\Compression\ArchiveCreator;

/**
 * @package Chamilo\Libraries\File\Compression\ArchiveCreator
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ArchiveFile extends ArchiveItem
{
    protected string $originalPath;

    public function getOriginalPath(): string
    {
        return $this->originalPath;
    }

    public function setOriginalPath(string $originalPath): static
    {
        $this->originalPath = $originalPath;

        return $this;
    }
}