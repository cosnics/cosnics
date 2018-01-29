<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Core\Repository\Publication\Service\ContentObjectPublicationManagerInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Manages the communication between the repository and the publications of content objects. This service is used
 * to determine whether or not a content object can be deleted, can be edited, ...
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationManager implements ContentObjectPublicationManagerInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService
     */
    protected $assignmentService;

    /**
     * @var \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService
     */
    protected $learningPathAssignmentService;

    /**
     * ContentObjectPublicationManager constructor.
     *
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService $learningPathAssignmentService
     */
    public function __construct(
        AssignmentService $assignmentService, LearningPathAssignmentService $learningPathAssignmentService
    )
    {
        $this->assignmentService = $assignmentService;
        $this->learningPathAssignmentService = $learningPathAssignmentService;
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
        if($this->assignmentService->isContentObjectUsedAsEntry($contentObject))
        {
            return false;
        }

        if($this->learningPathAssignmentService->isContentObjectUsedAsEntry($contentObject))
        {
            return false;
        }

        return true;
    }
}