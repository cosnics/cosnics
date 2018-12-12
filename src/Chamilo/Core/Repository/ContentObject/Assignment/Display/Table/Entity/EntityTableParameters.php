<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\RightsService;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntityTableParameters
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface
     */
    protected $assignmentServiceBridge;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface
     */
    protected $feedbackServiceBridge;

    /**
     * @var RightsService
     */
    protected $rightService;

    /**
     * @var int
     */
    protected $entityType;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var string[]
     */
    protected $entityProperties;

    /**
     * @var bool
     */
    protected $entityHasMultipleMembers;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment
     */
    protected $assignment;

    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    protected $user;

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface
     */
    public function getAssignmentServiceBridge(): AssignmentServiceBridgeInterface
    {
        return $this->assignmentServiceBridge;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface $assignmentServiceBridge
     */
    public function setAssignmentServiceBridge(AssignmentServiceBridgeInterface $assignmentServiceBridge)
    {
        $this->assignmentServiceBridge = $assignmentServiceBridge;
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface
     */
    public function getFeedbackServiceBridge(): FeedbackServiceBridgeInterface
    {
        return $this->feedbackServiceBridge;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface $feedbackServiceBridge
     */
    public function setFeedbackServiceBridge(FeedbackServiceBridgeInterface $feedbackServiceBridge)
    {
        $this->feedbackServiceBridge = $feedbackServiceBridge;
    }

    /**
     * @return int
     */
    public function getEntityType(): int
    {
        return $this->entityType;
    }

    /**
     * @param int $entityType
     */
    public function setEntityType(int $entityType)
    {
        $this->entityType = $entityType;
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment
     */
    public function getAssignment(): Assignment
    {
        return $this->assignment;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     */
    public function setAssignment(
        Assignment $assignment
    )
    {
        $this->assignment = $assignment;
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return RightsService
     */
    public function getRightService(): RightsService
    {
        return $this->rightService;
    }

    /**
     * @param RightsService $rightService
     */
    public function setRightService(RightsService $rightService)
    {
        $this->rightService = $rightService;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return string[]
     */
    public function getEntityProperties(): array
    {
        return $this->entityProperties;
    }

    /**
     * @param string[] $entityProperties
     */
    public function setEntityProperties(array $entityProperties)
    {
        $this->entityProperties = $entityProperties;
    }

    /**
     * @return bool
     */
    public function hasEntityMultipleMembers(): bool
    {
        return $this->entityHasMultipleMembers;
    }

    /**
     * @param bool $entityHasMultipleMembers
     */
    public function setEntityHasMultipleMembers(bool $entityHasMultipleMembers)
    {
        $this->entityHasMultipleMembers = $entityHasMultipleMembers;
    }


}