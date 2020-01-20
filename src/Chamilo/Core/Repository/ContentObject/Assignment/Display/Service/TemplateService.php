<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class TemplateService
{
    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * TemplateService constructor.
     *
     * @param ContentObjectRepository $contentObjectRepository
     */
    public function __construct(ContentObjectRepository $contentObjectRepository)
    {
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     * @param Assignment $assignment
     * @param AssignmentServiceBridgeInterface $assignmentServiceBridge
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return Page[]
     *
     * @throws \Exception
     */
    public function getTemplatesForAssignment(
        Assignment $assignment, AssignmentServiceBridgeInterface $assignmentServiceBridge,
        int $entityType, int $entityIdentifier
    )
    {
        $templates = [];

        if ($assignment->hasPageTemplate())
        {
            $template = $assignment->getInMemoryPageObjectFromTemplate();

            if ($assignment->useLastEntryAsTemplate())
            {
                $entries = $assignmentServiceBridge->findEntriesForEntityTypeAndId($entityType, $entityIdentifier);

                if (count($entries) > 0)
                {
                    $lastEntry = null;
                    $entriesArray = $entries->getArrayCopy();

                    do
                    {
                        $possibleEntry = array_pop($entriesArray);
                        if ($possibleEntry[ContentObject::PROPERTY_TYPE] == Page::class)
                        {
                            $lastEntry = $possibleEntry;
                            break;
                        }
                    }
                    while (count($entriesArray) > 0);

                    if (!empty($lastEntry))
                    {
                        $entryContentObject = $this->contentObjectRepository->findById(
                            $lastEntry[Entry::PROPERTY_CONTENT_OBJECT_ID]
                        );

                        $entryContentObject->set_title($template->get_title());
                        /** @var Page $template */
                        $template = $entryContentObject;
                    }
                }
            }

            $templates = [$template];
        }

        return $templates;
    }

}
