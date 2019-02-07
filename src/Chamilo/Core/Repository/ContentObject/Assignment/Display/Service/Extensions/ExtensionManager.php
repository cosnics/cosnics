<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\Extensions;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EntryComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\Extensions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExtensionManager implements ExtensionInterface
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\Extensions\ExtensionInterface[]
     */
    protected $extensions = [];

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\Extensions\ExtensionInterface $extension
     */
    public function addExtension(ExtensionInterface $extension)
    {
        $this->extensions[] = $extension;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EntryComponent $entryComponent
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function extendEntryViewerTitle(EntryComponent $entryComponent, Assignment $assignment, Entry $entry, User $user)
    {
        $html = [];

        foreach($this->extensions as $extension)
        {
            $html[] = $extension->extendEntryViewerTitle($entryComponent, $assignment, $entry, $user);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EntryComponent $entryComponent
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function extendEntryViewerParts(EntryComponent $entryComponent, Assignment $assignment, Entry $entry, User $user)
    {
        $html = [];

        foreach($this->extensions as $extension)
        {
            $html[] = $extension->extendEntryViewerParts($entryComponent, $assignment, $entry, $user);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function entryCreated(Assignment $assignment, Entry $entry, User $user)
    {
        foreach($this->extensions as $extension)
        {
            $extension->entryCreated($assignment, $entry, $user);
        }
    }
}