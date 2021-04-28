<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Bridge\RubricBridgeInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * Class RubricBridge
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
class RubricBridge implements RubricBridgeInterface
{
    /**
     * @var EvaluationServiceBridgeInterface
     */
    protected $evaluationServiceBridge;

    /**
     * @var EvaluationEntry
     */
    protected $evaluationEntry;

    //protected $scoreService;

    /**
     * RubricBridge constructor.
     *
     * @param EvaluationServiceBridgeInterface $evaluationServiceBridge
     */
    public function __construct(EvaluationServiceBridgeInterface $evaluationServiceBridge)
    {
        $this->evaluationServiceBridge = $evaluationServiceBridge;
    }

    /**
     * @param EvaluationEntry $entry
     */
    public function setEvaluationEntry(EvaluationEntry $entry)
    {
        $this->evaluationEntry = $entry;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier()
    {
        return new ContextIdentifier(get_class($this->evaluationEntry), $this->evaluationEntry->getId());
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
        return $this->evaluationServiceBridge->getUsersForEntity($this->evaluationEntry->getEntityType(), $this->evaluationEntry->getEntityId());
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
