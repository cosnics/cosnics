<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge;

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
     * @var Entry
     */
    protected $entry;

    /**
     * @var ScoreService
     */
    protected $scoreService;

    /**
     * RubricBridge constructor.
     *
     * @param ScoreService $scoreService
     */
    public function __construct(/*ScoreService $scoreService*/)
    {
        //$this->scoreService = $scoreService;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier()
    {
        return null;
        //return new ContextIdentifier(get_class($this->entry), $this->entry->getId());
    }

    /**
     * @return string|void
     */
    public function getEntityName()
    {
        return '';
        /*return $this->assignmentServiceBridge->renderEntityNameByEntityTypeAndEntityId(
            $this->entry->getEntityType(), $this->entry->getEntityId()
        );*/
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getTargetUsers()
    {
        return [];
        /*return $this->assignmentServiceBridge->getUsersForEntity(
            $this->entry->getEntityType(), $this->entry->getEntityId()
        );*/
    }

    /**
     * @param User $user
     * @param float $totalScore
     * @param float $maxScore
     */
    public function saveScore(User $user, float $totalScore, float $maxScore)
    {
        /*if (!$this->entry instanceof Entry)
        {
            return;
        }

        if (!$this->assignmentServiceBridge->canEditAssignment())
        {
            return;
        }

        $relativeScore = round(($totalScore / $maxScore) * 100);

        $this->scoreService->createOrUpdateScoreForEntry($this->entry, $relativeScore, $user);*/
    }
}
