<?php

namespace Chamilo\Core\Repository\Feedback\Bridge;

use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface FeedbackBridgeInterface
 *
 * @package Chamilo\Core\Repository\Feedback\Bridge
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface FeedbackServiceBridgeInterface
{
    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback $feedbackContentObject
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
     */
    public function createFeedback(
        User $user, \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback $feedbackContentObject
    );

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     */
    public function updateFeedback(Feedback $feedback);

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     */
    public function deleteFeedback(Feedback $feedback);

    /**
     * @param int $count
     * @param int $offset
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback[] | mixed[]
     */
    public function getFeedback($count = null, $offset = null);

    /**
     * @return int
     */
    public function countFeedback();

    /**
     * @param int $feedbackId
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
     */
    public function getFeedbackById($feedbackId);
}