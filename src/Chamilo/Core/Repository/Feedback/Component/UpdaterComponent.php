<?php
namespace Chamilo\Core\Repository\Feedback\Component;

use Chamilo\Core\Repository\Feedback\Form\FeedbackForm;
use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * Controller to update the controlled vocabulary
 *
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdaterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $feedback_id = Request::get(self::PARAM_FEEDBACK_ID);
        $this->set_parameter(self::PARAM_FEEDBACK_ID, $feedback_id);

        $feedback = $this->feedbackServiceBridge->getFeedbackById($feedback_id);

        if (!$feedback instanceof Feedback)
        {
            throw new ObjectNotExistException(Translation::getInstance()->getTranslation('Feedback'), $feedback_id);
        }

        if (!$this->feedbackRightsServiceBridge->canEditFeedback($feedback))
        {
            throw new NotAllowedException();
        }

        $form = new FeedbackForm($this, $this->get_url(), $feedback);

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $feedback->set_comment($values[Feedback::PROPERTY_COMMENT]);
                $this->feedbackServiceBridge->updateFeedback($feedback);

                $message = Translation::get(
                    'ObjectUpdated',
                    array('OBJECT' => Translation::get('Feedback')),
                    StringUtilities::LIBRARIES
                );

                $success = true;
            }
            catch (Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirectWithMessage($message, !$success, array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        else
        {
            return $form->toHtml();
        }
    }
}