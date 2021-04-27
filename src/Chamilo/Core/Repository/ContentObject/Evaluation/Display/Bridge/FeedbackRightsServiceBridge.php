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
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    protected $currentUser;

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
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     */
    public function setCurrentUser(User $currentUser)
    {
        $this->currentUser = $currentUser;
    }

    /**
     * @return bool
     */
    public function canCreateFeedback()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canViewFeedback()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canViewPrivateFeedback()
    {
        return $this->evaluationServiceBridge->canEditEvaluation();
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function canEditFeedback(Feedback $feedback)
    {
        return $feedback->get_user_id() == $this->currentUser->getId();
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function canDeleteFeedback(Feedback $feedback)
    {
        return $feedback->get_user_id() == $this->currentUser->getId();
    }
}