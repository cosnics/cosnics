<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Form\PeerAssessmentViewerForm;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class ViewerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $user_id = $this->get_user_id();
        $attempt_id = Request::get(self::PARAM_ATTEMPT);

        $status = $this->get_user_attempt_status($user_id, $attempt_id);

        if (! is_null($status->get_closed()))
        {
            throw new NotAllowedException();
        }

        $this->set_parameter(self::PARAM_ATTEMPT, $attempt_id);

        $form = new PeerAssessmentViewerForm($this);

        $html = array();

        $form->setDefaults(
            array(
                PeerAssessmentViewerForm::PARAM_SCORES => $this->get_user_scores_given($user_id, $attempt_id),
                PeerAssessmentViewerForm::PARAM_FEEDBACK => $this->get_user_feedback_given($user_id, $attempt_id)));

        // if ($form->isSubmitted())
        if ($form->validate())
        {
            $values = $form->exportValues();
            $saved = $this->save_scores($user_id, $attempt_id, $values[$form::PARAM_SCORES]);
            $saved_feedback = $this->save_feedback($user_id, $attempt_id, $values[$form::PARAM_FEEDBACK]);

            if (! $saved)
            {
                $html[] = $this->display_error_message(Translation::get('NotSaved'));
            }
            else
            {
                $this->redirect(
                    Translation::get('Saved'),
                    false,
                    array(self::PARAM_ACTION => self::ACTION_VIEW_USER_ATTEMPT_STATUS));
            }
        }
        else
        {
            if ($form->has_validation_errors())
            {
                $html[] = $this->display_error_message(Translation::get($form->get_validation_errors()));
            }
        }

        $attempt = $this->get_attempt($attempt_id);

        $group = $this->get_user_group($user_id);

        if (isset($group))
        {
            $processor = $this->get_root_content_object()->get_result_processor();
            $html[] = $this->render_header();

            if ($this->get_root_content_object()->get_assessment_type() != PeerAssessment::TYPE_FEEDBACK)
            {
                $html[] = '<h3>' . $processor->get_intro_title() . '</h3><p>' . $processor->get_intro_description() .
                     '</p>';
            }

            $html[] = '<h2>' . $attempt->get_title() . '</h2>';
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();
        }
        else
        {
            if ($this->is_allowed(self::EDIT_RIGHT))
            {
                $params[self::PARAM_ACTION] = self::ACTION_OVERVIEW_STATUS;
            }
            else
            {
                $params[self::PARAM_ACTION] = self::ACTION_VIEW_USER_ATTEMPT_STATUS;
            }
            $this->redirect(Translation::get('NoGroupSubscription'), 1, $params);
        }

        return implode(PHP_EOL, $html);
    }
}
