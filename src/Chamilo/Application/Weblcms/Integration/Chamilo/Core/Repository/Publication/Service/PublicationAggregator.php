<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\AssignmentService;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Service\LearningPathAssignmentService;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Manages the communication between the repository and the publications of content objects. This service is used
 * to determine whether or not a content object can be deleted, can be edited, ...
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationAggregator implements PublicationAggregatorInterface
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
     * PublicationAggregator constructor.
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

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countPublicationAttributes(
        int $type = PublicationInterface::ATTRIBUTES_TYPE_OBJECT, int $objectIdentifier,
        Condition $condition = null
    )
    {
        // TODO: Implement countPublicationAttributes() method.
    }

    /**
     * @param integer $type
     * @param integer $objectIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes[]
     */
    public function getContentObjectPublicationsAttributes(
        int $type = PublicationInterface::ATTRIBUTES_TYPE_OBJECT, int $objectIdentifier, Condition $condition = null,
        int $count = null, int $offset = null, array $orderProperties = null
    )
    {
        // TODO: Implement getContentObjectPublicationsAttributes() method.
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    public function deleteContentObjectPublications(ContentObject $contentObject)
    {
        // TODO: Implement deleteContentObjectPublications() method.
    }

    /**
     * @param integer $contentObjectIdentifier
     *
     * @return boolean
     */
    public function isContentObjectPublished(int $contentObjectIdentifier)
    {
        // TODO: Implement isContentObjectPublished() method.
    }

    /**
     * @param integer[] $contentObjectIdentifiers
     *
     * @return boolean
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers)
    {
        // TODO: Implement areContentObjectsPublished() method.
    }

    /**
     * @param integer $contentObjectIdentifier
     *
     * @return boolean
     */
    public function canContentObjectBeEdited(int $contentObjectIdentifier)
    {
        // TODO: Implement canContentObjectBeEdited() method.
    }
}