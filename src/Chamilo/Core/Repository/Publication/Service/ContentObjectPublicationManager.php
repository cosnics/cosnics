<?php

namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Manages the communication between the repository and the publications of content objects. This service is used
 * to determine whether or not a content object can be deleted, can be edited, ...
 *
 * @package Chamilo\Core\Repository\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationManager implements ContentObjectPublicationManagerInterface
{
    /**
     * @var \Chamilo\Core\Repository\Publication\Service\ContentObjectPublicationManagerInterface[]
     */
    protected $contentObjectPublicationManagers;

    /**
     * ContentObjectPublicationManager constructor.
     */
    public function __construct()
    {
        $this->contentObjectPublicationManagers = [];
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Service\ContentObjectPublicationManagerInterface $contentObjectPublicationManager
     */
    public function addContentObjectPublicationManager(
        ContentObjectPublicationManagerInterface $contentObjectPublicationManager
    )
    {
        $this->contentObjectPublicationManagers[] = $contentObjectPublicationManager;
    }

    /**
     * Returns whether or not a content object can be unlinked
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function canContentObjectBeUnlinked(ContentObject $contentObject)
    {
        foreach ($this->contentObjectPublicationManagers as $contentObjectPublicationManager)
        {
            if (!$contentObjectPublicationManager->canContentObjectBeUnlinked($contentObject))
            {
                return false;
            }
        }

        return true;
    }
}