<?php
namespace Chamilo\Core\Repository\Feedback\Bridge;

use Chamilo\Core\Repository\Feedback\FeedbackSupport;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;

/**
 * @package Chamilo\Core\Repository\Feedback\Bridge
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @deprecated only used to provide a temporary solution between the old FeedbackSupport interface and the new bridge architecture. Implement your own FeedbackBridge
 */
class FeedbackRightsServiceBridgeAdapter implements FeedbackRightsServiceBridgeInterface
{
    /**
     * @var \Chamilo\Core\Repository\Feedback\FeedbackSupport
     */
    protected $feedbackSupportComponent;

    /**
     * FeedbackBridgeAdapter constructor.
     *
     * @param \Chamilo\Core\Repository\Feedback\FeedbackSupport $feedbackSupportComponent
     */
    public function __construct(FeedbackSupport $feedbackSupportComponent)
    {
        $this->feedbackSupportComponent = $feedbackSupportComponent;
    }

    /**
     * @return bool
     */
    public function canCreateFeedback()
    {
        return $this->feedbackSupportComponent->is_allowed_to_create_feedback();
    }

    /**
     * @return bool
     */
    public function canViewFeedback()
    {
        return $this->feedbackSupportComponent->is_allowed_to_view_feedback();
    }

    /**
     * @return bool
     */
    public function canViewPrivateFeedback()
    {
        return false;
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function canEditFeedback(Feedback $feedback)
    {
        return $this->feedbackSupportComponent->is_allowed_to_update_feedback($feedback);
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function canDeleteFeedback(Feedback $feedback)
    {
        return $this->feedbackSupportComponent->is_allowed_to_delete_feedback($feedback);
    }
}