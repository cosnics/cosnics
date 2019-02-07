<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\Extensions;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EntryComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface ExtensionInterface
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\Extensions
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface ExtensionInterface
{
    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EntryComponent $entryComponent
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function extendEntryViewerTitle(EntryComponent $entryComponent, Assignment $assignment, Entry $entry, User $user);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EntryComponent $entryComponent
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function extendEntryViewerParts(EntryComponent $entryComponent, Assignment $assignment, Entry $entry, User $user);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function entryCreated(Assignment $assignment, Entry $entry, User $user);
}