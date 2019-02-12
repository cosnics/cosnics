<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table;

use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryPlagiarismResultTableParameters
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface
     */
    protected $entryPlagiarismResultServiceBridge;

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
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment
     */
    protected $assignment;

    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    protected $user;

    /**
     * @var string
     */
    protected $entryClassName;

    /**
     * @var string
     */
    protected $scoreClassName;

    /**
     * @var string
     */
    protected $entryPlagiarismResultClassName;

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface
     */
    public function getEntryPlagiarismResultServiceBridge(): EntryPlagiarismResultServiceBridgeInterface
    {
        return $this->entryPlagiarismResultServiceBridge;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     */
    public function setEntryPlagiarismResultServiceBridge(
        EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
    )
    {
        $this->entryPlagiarismResultServiceBridge = $entryPlagiarismResultServiceBridge;
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
     * @return string
     */
    public function getEntryClassName(): string
    {
        return $this->entryClassName;
    }

    /**
     * @param string $entryClassName
     */
    public function setEntryClassName(string $entryClassName): void
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
    public function setScoreClassName(string $scoreClassName): void
    {
        $this->scoreClassName = $scoreClassName;
    }

    /**
     * @return string
     */
    public function getEntryPlagiarismResultClassName(): string
    {
        return $this->entryPlagiarismResultClassName;
    }

    /**
     * @param string $entryPlagiarismResultClassName
     */
    public function setEntryPlagiarismResultClassName(string $entryPlagiarismResultClassName): void
    {
        $this->entryPlagiarismResultClassName = $entryPlagiarismResultClassName;
    }
}