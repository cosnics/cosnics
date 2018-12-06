<?php

namespace Chamilo\Core\Repository\Feedback\Bridge;

use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;

/**
 * @package Chamilo\Core\Repository\Feedback\Bridge
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface FeedbackRightsServiceBridgeInterface
{
    /**
     * @return bool
     */
    public function canCreateFeedback();

    /**
     * @return bool
     */
    public function canViewFeedback();

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function canEditFeedback(Feedback $feedback);

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function canDeleteFeedback(Feedback $feedback);
}