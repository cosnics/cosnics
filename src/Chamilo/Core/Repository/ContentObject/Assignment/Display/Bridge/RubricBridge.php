<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\ScoreService;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Bridge\RubricBridgeInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * Class RubricBridge
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge
 */
class RubricBridge implements RubricBridgeInterface
{
    /**
     * @var AssignmentServiceBridgeInterface
     */
    protected $assignmentServiceBridge;

    /**
     * @var Entry
     */
    protected $entry;

    /**
     * @var ScoreService
     */
    protected $scoreService;

    /**
     * @var string
     */
    protected $entryURL;

    /**
     * @var bool
     */
    protected $allowCreateFromExistingRubric = false;

    /**
     * RubricBridge constructor.
     *
     * @param AssignmentServiceBridgeInterface $assignmentServiceBridge
     * @param ScoreService $scoreService
     */
    public function __construct(AssignmentServiceBridgeInterface $assignmentServiceBridge, ScoreService $scoreService)
    {
        $this->assignmentServiceBridge = $assignmentServiceBridge;
        $this->scoreService = $scoreService;
    }

    /**
     * @param Entry $entry
     */
    public function setEntry(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier()
    {
        return new ContextIdentifier(get_class($this->entry), $this->entry->getId());
    }

    /**
     * @return string|void
     */
    public function getEntityName()
    {
        return $this->assignmentServiceBridge->renderEntityNameByEntityTypeAndEntityId(
            $this->entry->getEntityType(), $this->entry->getEntityId()
        );
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getTargetUsers()
    {
        return $this->assignmentServiceBridge->getUsersForEntity(
            $this->entry->getEntityType(), $this->entry->getEntityId()
        );
    }

    /**
     * @param User $user
     * @param float $totalScore
     * @param float $maxScore
     */
    public function saveScore(User $user, float $totalScore, float $maxScore)
    {
        if (!$this->entry instanceof Entry)
        {
            return;
        }

        if (!$this->assignmentServiceBridge->canEditAssignment())
        {
            return;
        }

        $relativeScore = round(($totalScore / $maxScore) * 100);

        $this->scoreService->createOrUpdateScoreForEntry($this->entry, $relativeScore, $user);
    }

    public function getPostSaveRedirectParameters()
    {
        return null;
    }

    /**
     * @param string $url
     */
    public function setEntryURL(string $url)
    {
        $this->entryURL = $url;
    }

    /**
     * @return string
     */
    public function getEntryURL(): string
    {
        return $this->entryURL;
    }

    /**
     * @param bool $allow
     */
    public function setAllowCreateFromExistingRubric(bool $allow)
    {
        $this->allowCreateFromExistingRubric = $allow;
    }

    /**
     * @return bool
     */
    public function getAllowCreateFromExistingRubric(): bool
    {
        return $this->allowCreateFromExistingRubric;
    }
}
