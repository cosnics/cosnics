<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\RightsService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackRightsServiceBridgeInterface;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class FeedbackRightsServiceBridge implements FeedbackRightsServiceBridgeInterface
{
    /**
     * @var EvaluationServiceBridgeInterface
     */
    protected $evaluationServiceBridge;

    /**
     * @var RightsService
     */
    protected $rightsService;

    /**
     * @var User
     */
    protected $currentUser;

    /**
     * @var EvaluationEntry
     */
    protected $evaluationEntry;

    /**
     * FeedbackRightsServiceBridge constructor.
     *
     * @param EvaluationServiceBridgeInterface $evaluationServiceBridge
     * @param RightsService $rightsService
     */
    public function __construct(EvaluationServiceBridgeInterface $evaluationServiceBridge, RightsService $rightsService)
    {
        $this->evaluationServiceBridge = $evaluationServiceBridge;
        $this->rightsService = $rightsService;
    }

    /**
     * @param User $currentUser
     */
    public function setCurrentUser(User $currentUser)
    {
        $this->currentUser = $currentUser;
    }

    /**
     * @param EvaluationEntry $evaluationEntry
     */
    public function setEvaluationEntry(EvaluationEntry $evaluationEntry)
    {
        $this->evaluationEntry = $evaluationEntry;
    }

    /**
     * @return bool
     */
    public function canCreateFeedback(): bool
    {
        return $this->rightsService->canUserViewEntry($this->currentUser, $this->evaluationEntry);
    }

    /**
     * @return bool
     */
    public function canViewFeedback(): bool
    {
        return $this->rightsService->canUserViewEntry($this->currentUser, $this->evaluationEntry);
    }

    /**
     * @return bool
     */
    public function canViewPrivateFeedback(): bool
    {
        return $this->evaluationServiceBridge->canEditEvaluation();
    }

    /**
     * @param Feedback $feedback
     *
     * @return bool
     */
    public function canEditFeedback(Feedback $feedback): bool
    {
        return $feedback->get_user_id() == $this->currentUser->getId();
    }

    /**
     * @param Feedback $feedback
     *
     * @return bool
     */
    public function canDeleteFeedback(Feedback $feedback): bool
    {
        return $feedback->get_user_id() == $this->currentUser->getId();
    }
}