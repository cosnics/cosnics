<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge;

use Chamilo\Core\Repository\Feedback\Bridge\FeedbackRightsServiceBridgeInterface;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Bridge\Feedback
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FeedbackRightsServiceBridge implements FeedbackRightsServiceBridgeInterface
{
    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    protected $currentUser;

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