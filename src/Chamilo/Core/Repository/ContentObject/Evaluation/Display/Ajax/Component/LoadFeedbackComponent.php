<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LoadFeedbackComponent extends Manager
{
    /**
     */
    function run()
    {
        try
        {
            $evaluation = $this->get_root_content_object();

            if (!$evaluation instanceof Evaluation) {
                $this->throwUserException('EvaluationNotFound');
            }

            $this->initializeEntry();
            $feedbackItems = $this->getFeedbackServiceBridge()->getFeedback();

            $feedback = array();
            foreach ($feedbackItems as $feedbackItem)
            {
                $contentObject = $this->getContentObjectRepository()->findById($feedbackItem->getFeedbackContentObjectId());
                $profilePhotoUrl = new Redirect(
                    array(
                        Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                        Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                        \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $feedbackItem->get_user()->get_id()
                    )
                );
                $feedback[] = [
                    'user' => $feedbackItem->get_user()->get_fullname(),
                    'photo' => $profilePhotoUrl->getUrl(),
                    'date' => $this->format_date($feedbackItem->get_creation_date()),
                    'content' => $contentObject->get_description()
                ];
            }

            $result = new JsonAjaxResult(200, $feedback);
            $result->display();
        }
        catch (\Exception $ex)
        {
            $result = new JsonAjaxResult();
            $result->set_result_code(500);
            $result->set_result_message($ex->getMessage());
            $result->display();
        }
    }

    /**
     *
     * @param int $date
     *
     * @return string
     */
    public function format_date($date)
    {
        $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);

        return DatetimeUtilities::format_locale_date($date_format, $date);
    }

}
