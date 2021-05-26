<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EvaluationEntryService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Interfaces\ConfirmRubricScoreInterface;
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
     * @var EvaluationEntryService
     */
    protected $evaluationEntryService;

    /**
     * @var EvaluationServiceBridgeInterface
     */
    protected $evaluationServiceBridge;

    /**
     * @var EvaluationEntry
     */
    protected $evaluationEntry;

    /**
     * @var ConfirmRubricScoreInterface
     */
    protected $confirmRubricScore;

    //protected $scoreService;

    /**
     * @var string[]
     */
    protected $postSaveRedirectParameters;

    /**
     * RubricBridge constructor.
     *
     * @param EvaluationServiceBridgeInterface $evaluationServiceBridge
     * @param EvaluationEntryService $evaluationEntryService
     */
    public function __construct(EvaluationServiceBridgeInterface $evaluationServiceBridge, EvaluationEntryService $evaluationEntryService)
    {
        $this->evaluationServiceBridge = $evaluationServiceBridge;
        $this->evaluationEntryService = $evaluationEntryService;
    }

    /**
     * @param ConfirmRubricScoreInterface $confirmRubricScore
     */
    public function setConfirmRubricScore(ConfirmRubricScoreInterface $confirmRubricScore)
    {
        $this->confirmRubricScore = $confirmRubricScore;
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
        return $this->evaluationServiceBridge->getUsersForEntity($this->evaluationEntry->getEntityId());
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

        $relativeScore = round(($totalScore / $maxScore) * 100);

        if ($this->evaluationEntryService->getEvaluationEntryScore($this->evaluationEntry->getId()))
        {
            $this->confirmRubricScore->registerRubricScore($relativeScore);
            return;
        }

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
