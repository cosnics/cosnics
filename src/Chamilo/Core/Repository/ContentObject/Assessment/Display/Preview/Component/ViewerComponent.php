<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt\AbstractAttempt;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Interfaces\AssessmentDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Preview\DummyAttempt;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Preview\PreviewStorage;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assessment\Display\Preview\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\Assessment\Display\Preview\Manager implements
    AssessmentDisplaySupport
{

    /**
     *
     * @var \core\repository\content_object\assessment\display\DummyAttempt
     */
    private $attempt;

    /**
     *
     * @var DummyQuestionAttempt[]
     */
    private $question_attempts;

    public function run()
    {
        $this->attempt = PreviewStorage :: getInstance()->retrieve_assessment_attempt(
            $this->get_root_content_object()->get_id());

        if (! $this->attempt instanceof AbstractAttempt)
        {
            $this->attempt = $this->create_attempt();
        }

        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\ContentObject\Assessment\Display\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    /**
     *
     * @throws \Exception
     * @return \core\repository\content_object\assessment\display\DummyAttempt
     */
    private function create_attempt()
    {
        $attempt = new DummyAttempt();
        $attempt->set_content_object_id($this->get_root_content_object()->get_id());
        $attempt->set_user_id($this->get_user_id());
        $attempt->set_total_score(0);
        $attempt->set_start_time(time());

        if ($attempt->create())
        {
            return $attempt;
        }
        else
        {
            throw new \Exception(Translation :: get('DummyAttemptNotCreated'));
        }
    }

    /**
     * ViewerComponent mode, so always return true.
     *
     * @param $right
     * @return boolean
     */
    public function is_allowed($right)
    {
        return true;
    }

    /**
     * ViewerComponent mode is launched in standalone mode, so there's nothing to go back to.
     *
     * @return void
     */
    public function get_assessment_back_url()
    {
        return null;
    }

    /**
     * ViewerComponent mode is launched in standalone mode, so there's nothing to continue to.
     *
     * @return void
     */
    public function get_assessment_continue_url()
    {
        return null;
    }

    public function get_assessment_current_url()
    {
        return null;
    }

    public function get_assessment_configuration()
    {
        return new Configuration();
    }

    public function get_assessment_parameters()
    {
        return array();
    }

    // FUNCTIONS FOR COMPLEX DISPLAY SUPPORT
    public function is_allowed_to_edit_content_object()
    {
        return true;
    }

    public function is_allowed_to_view_content_object()
    {
        return true;
    }

    public function is_allowed_to_add_child()
    {
        return true;
    }

    public function is_allowed_to_delete_child()
    {
        return true;
    }

    public function is_allowed_to_delete_feedback()
    {
        return true;
    }

    public function is_allowed_to_edit_feedback()
    {
        return true;
    }

    /**
     *
     * @see \core\repository\content_object\assessment\display\AssessmentDisplaySupport::register_question_ids()
     */
    public function register_question_ids($question_complex_ids)
    {
        foreach ($question_complex_ids as $question_complex_id)
        {
            $question_attempt = new DummyQuestionAttempt();
            $question_attempt->set_attempt_id($this->attempt->get_id());
            $question_attempt->set_question_complex_id($question_complex_id);
            $question_attempt->set_answer('');
            $question_attempt->set_score(0);
            $question_attempt->set_feedback('');
            $question_attempt->set_hint(0);

            if ($question_attempt->create())
            {
                $this->question_attempts[$question_complex_id] = $question_attempt;
            }
            else
            {
                throw new \Exception(Translation :: get('DummyQuestionAttemptNotCreated'));
            }
        }
    }

    /**
     *
     * @see \core\repository\content_object\assessment\display\AssessmentDisplaySupport::get_registered_question_ids()
     */
    public function get_registered_question_ids()
    {
        $question_ids = array();

        $question_attempts = $this->get_assessment_question_attempts();
        foreach ($question_attempts as $question_attempt)
        {
            $question_ids[] = $question_attempt->get_question_complex_id();
        }

        return $question_ids;
    }

    /**
     * Returns the assessment question attempts
     *
     * @return DummyQuestionAttempt[]
     */
    public function get_assessment_question_attempts()
    {
        if (is_null($this->question_attempts))
        {
            $this->question_attempts = PreviewStorage :: getInstance()->retrieve_assessment_question_attempts(
                $this->attempt);
        }

        return $this->question_attempts;
    }

    /**
     *
     * @see \core\repository\content_object\assessment\display\AssessmentDisplaySupport::save_assessment_answer()
     */
    public function save_assessment_answer($complex_question_id, $answer, $score, $hint)
    {
        $question_attempt = $this->get_assessment_question_attempt($complex_question_id);

        $question_attempt->set_answer($answer);
        $question_attempt->set_score($score);
        $question_attempt->set_hint($hint);

        return $question_attempt->update();
    }

    /**
     *
     * @see \core\repository\content_object\assessment\display\AssessmentDisplaySupport::save_assessment_result()
     */
    public function save_assessment_result($total_score)
    {
        $this->attempt->set_total_score($total_score);
        $this->attempt->set_end_time(time());
        $this->attempt->set_status(AbstractAttempt :: STATUS_COMPLETED);

        $this->attempt->set_total_time(
            $this->attempt->get_total_time() + ($this->attempt->get_end_time() - $this->attempt->get_start_time()));

        return $this->attempt->update();
    }

    /**
     *
     * @see \core\repository\content_object\assessment\display\AssessmentDisplaySupport::get_assessment_current_attempt_id()
     */
    public function get_assessment_current_attempt_id()
    {
        return $this->attempt->get_id();
    }

    /**
     *
     * @see \core\repository\content_object\assessment\display\AssessmentDisplaySupport::get_assessment_question_attempt()
     */
    public function get_assessment_question_attempt($complex_question_id)
    {
        return $this->question_attempts[$complex_question_id];
    }

    /**
     *
     * @see \core\repository\display\PreviewResetSupport::reset()
     */
    public function reset()
    {
        return PreviewStorage :: getInstance()->reset($this->get_root_content_object()->get_id());
    }
}
