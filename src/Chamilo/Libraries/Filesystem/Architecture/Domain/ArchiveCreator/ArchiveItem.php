<?php
namespace Chamilo\Libraries\Filesystem\Architecture\Domain\ArchiveCreator;

/**
 * @package Chamilo\Libraries\Filesystem\Architecture\Domain\ArchiveCreator
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ArchiveItem
{
    protected string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}