<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryTableParameters
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
     * @var int
     */
    protected $entityType;

    /**
     * @var int
     */
    protected $entityId;

    /**
     * @var string
     */
    protected $entryClassName;

    /**
     * @var string
     */
    protected $scoreClassName;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment
     */
    protected $assignment;

    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    protected $user;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    protected $currentEntry;

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
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * @param int $entityId
     */
    public function setEntityId(int $entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return string
     */
    public function getEntryClassName(): string
    {
        return $this->entryClassName;
    }

    /**
     * @param string $entryClassName
     */
    public function setEntryClassName(string $entryClassName)
    {
        $this->entryClassName = $entryClassName;
    }

    /**
     * @return string
     */
    public function getScoreClassName(): string
    {
        return $this->scoreClassName;
    }

    /**
     * @param string $scoreClassName
     */
    public function setScoreClassName(string $scoreClassName)
    {
        $this->scoreClassName = $scoreClassName;
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
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function getCurrentEntry(): Entry
    {
        return $this->currentEntry;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $currentEntry
     */
    public function setCurrentEntry(Entry $currentEntry)
    {
        $this->currentEntry = $currentEntry;
    }


}