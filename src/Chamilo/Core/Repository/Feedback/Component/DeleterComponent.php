<?php
namespace Chamilo\Core\Repository\Feedback\Component;

use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * Controller to delete the feedback
 */
class DeleterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $feedback_ids = $this->getRequest()->query->get(self::PARAM_FEEDBACK_ID);

        try
        {
            if (empty($feedback_ids))
            {
                throw new NoObjectSelectedException(Translation::get('Feedback'));
            }

            if (!is_array($feedback_ids))
            {
                $feedback_ids = [$feedback_ids];
            }

            foreach ($feedback_ids as $feedback_id)
            {
                $feedback = $this->feedbackServiceBridge->getFeedbackById($feedback_id);

                if (!$this->feedbackRightsServiceBridge->canDeleteFeedback($feedback))
                {
                    throw new NotAllowedException();
                }

                $this->feedbackServiceBridge->deleteFeedback($feedback);
            }

            $success = true;
            $message = Translation::get(
                'ObjectDeleted', ['OBJECT' => Translation::get('Feedback')], StringUtilities::LIBRARIES
            );
        }
        catch (Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }

        $this->redirectWithMessage($message, !$success, [self::PARAM_ACTION => self::ACTION_BROWSE]);
    }
}