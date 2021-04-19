<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class SaveNewFeedbackComponent extends Manager
{
    public function run(): string
    {
        try
        {
            $this->validateEvaluationEntityInput(); // todo: check if necessary
            $newFeedback = $this->getRequest()->getFromPost('comment') ?? '';
            $isPrivate = (bool) $this->getRequest()->getFromPost('is_private');

            $this->initializeEntry();

            $feedbackContentObject = $this->createFeedbackContentObject($this->getUser(), $newFeedback);
            $success = $feedbackContentObject->create();

            if ($success)
            {
                $feedback = $this->getFeedbackServiceBridge()->createFeedback($this->getUser(), $feedbackContentObject, $isPrivate);
                $success = $feedback instanceof Feedback;
            } else
            {
                // todo
            }

            if ($success)
            {
                $result = new JsonAjaxResult(200, ['status' => 'ok']);
                $result->display();
            } else
            {
                // todo
            }
        } catch (\Exception $ex)
        {
            $result = new JsonAjaxResult();
            $result->set_result_code(500);
            $result->set_result_message($ex->getMessage());
            $result->display();
        }
    }

    /**
     * @param User $user
     * @param $newFeedback
     *
     * @return \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback
     */
    protected function createFeedbackContentObject(User $user, $newFeedback): \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback
    {
        $feedbackContentObject = new \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback();
        $feedbackContentObject->set_owner_id($user->getId());
        $feedbackContentObject->set_description($newFeedback);
        $feedbackContentObject->set_title('Feedback');
        $feedbackContentObject->set_state(ContentObject::STATE_INACTIVE);
        return $feedbackContentObject;
    }
}
