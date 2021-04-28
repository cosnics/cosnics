<?php
namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
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
     * @var User
     */
    protected $currentUser;

    /**
     * FeedbackRightsServiceBridge constructor.
     *
     * @param EvaluationServiceBridgeInterface $evaluationServiceBridge
     */
    public function __construct(EvaluationServiceBridgeInterface $evaluationServiceBridge)
    {
        $this->evaluationServiceBridge = $evaluationServiceBridge;
    }

    /**
     * @param User $currentUser
     */
    public function setCurrentUser(User $currentUser)
    {
        $this->currentUser = $currentUser;
    }

    /**
     * @return bool
     */
    public function canCreateFeedback(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canViewFeedback(): bool
    {
        return true;
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