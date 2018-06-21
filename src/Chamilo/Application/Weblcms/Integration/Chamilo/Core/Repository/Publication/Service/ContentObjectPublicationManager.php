<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Core\Repository\Publication\Service\ContentObjectPublicationManagerInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;

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
     * @var UserService
     */
    protected $userService;

    /**
     * ContentObjectPublicationManager constructor.
     *
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService $assignmentService
     * @param \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService $learningPathAssignmentService
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function __construct(
        AssignmentService $assignmentService, LearningPathAssignmentService $learningPathAssignmentService,
        UserService $userService
    )
    {
        $this->assignmentService = $assignmentService;
        $this->learningPathAssignmentService = $learningPathAssignmentService;
        $this->userService = $userService;
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
        $user = $this->userService->findUserByIdentifier($contentObject->get_owner_id());
        $isTeacher = ($user instanceof User && $user->get_status() == User::STATUS_TEACHER);

        if ($this->assignmentService->isContentObjectUsedAsEntry($contentObject))
        {
            return ($isTeacher ?
                !$this->assignmentService->isContentObjectOwnerSameAsSubmitter($contentObject) :
                false);
        }

        if ($this->learningPathAssignmentService->isContentObjectUsedAsEntry($contentObject))
        {
            return ($isTeacher ?
                !$this->learningPathAssignmentService->isContentObjectOwnerSameAsSubmitter($contentObject) :
                false);
        }

        return true;
    }
}