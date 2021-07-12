<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Manager;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Security\Csrf\CsrfComponentInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Ajax\Component
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class SaveNewFeedbackComponent extends Manager implements CsrfComponentInterface
{
    public function run(): string
    {
        try
        {
            $this->validateEvaluationEntityInput(); // todo: check if necessary
            $evaluation = $this->getEvaluation();
            $newFeedback = $this->getRequest()->getFromPost('comment') ?? '';
            $isPrivate = $this->getRequest()->getFromPost('is_private') == 'true';
            $entityId = $this->getRequest()->getFromPost('entity_id');

            $evaluationEntry = $this->getEvaluationEntryService()->createEvaluationEntryIfNotExists($evaluation->getId(), $this->getEvaluationServiceBridge()->getContextIdentifier(), $this->getEvaluationServiceBridge()->getCurrentEntityType(), $entityId);

            $this->getFeedbackServiceBridge()->setEntryId($evaluationEntry->getId());

            $feedbackContentObject = $this->createFeedbackContentObject($this->getUser(), $newFeedback);
            $success = $feedbackContentObject->create();

            if ($success)
            {
                $feedbackItem = $this->getFeedbackServiceBridge()->createFeedback($this->getUser(), $feedbackContentObject, $isPrivate);
                $success = $feedbackItem instanceof Feedback;
            }
            else
            {
                // todo
            }

            if ($success)
            {
                $profilePhotoUrl = new Redirect(
                    array(
                        Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                        Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                        \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $feedbackItem->get_user()->get_id()
                    )
                );
                $feedback = [
                    'user' => $feedbackItem->get_user()->get_fullname(),
                    'photo' => $profilePhotoUrl->getUrl(),
                    'date' => $this->format_date($feedbackItem->get_creation_date()),
                    'content' => $feedbackContentObject->get_description(),
                    'isPrivate' => $feedbackItem->isPrivate()
                ];
                $result = new JsonAjaxResult(200, ['status' => 'ok', 'entity_id' => $entityId, 'feedback' => $feedback]);
                $result->display();
            } else
            {
                $result = new JsonAjaxResult(200, ['status' => 'fail']);
                $result->display();
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
