<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EntityService;
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
     * @var EntityService
     */
    protected $entityService;

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
     * @var string[]
     */
    protected $postSaveRedirectParameters;

    /**
     * RubricBridge constructor.
     *
     * @param EvaluationServiceBridgeInterface $evaluationServiceBridge
     * @param EntityService $entityService
     */
    public function __construct(EvaluationServiceBridgeInterface $evaluationServiceBridge, EntityService $entityService)
    {
        $this->evaluationServiceBridge = $evaluationServiceBridge;
        $this->entityService = $entityService;
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
    public function getContextIdentifier(): ContextIdentifier
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
    public function getTargetUsers(): array
    {
        return $this->evaluationServiceBridge->getUsersForEntity($this->evaluationEntry->getEntityType(), $this->evaluationEntry->getEntityId());
    }

    /**
     * @param User $user
     * @param float $totalScore
     * @param float $maxScore
     */
    public function saveScore(User $user, float $totalScore, float $maxScore)
    {
        if (!$this->evaluationEntry instanceof EvaluationEntry)
        {
            return;
        }

        if (!$this->evaluationServiceBridge->canEditEvaluation())
        {
            return;
        }

        if ($this->entityService->getEvaluationEntryScore($this->evaluationEntry->getId()))
        {
            return;
        }

        $relativeScore = round(($totalScore / $maxScore) * 100);

        $this->evaluationServiceBridge->saveEntryScoreForEntity($this->evaluationEntry->getEvaluationId(), $user->getId(), $this->evaluationEntry->getEntityId(), $relativeScore);
    }

    /**
     * @return string[]
     */
    public function getPostSaveRedirectParameters(): array
    {
        return $this->postSaveRedirectParameters;
    }

    /**
     * @param string[] $postSaveRedirectParameters
     */
    public function setPostSaveRedirectParameters(array $postSaveRedirectParameters)
    {
        $this->postSaveRedirectParameters = $postSaveRedirectParameters;
    }
}
