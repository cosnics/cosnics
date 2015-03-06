<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: assessment_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment
 */

/**
 * This tool allows a user to publish assessments in his or her course.
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    // Actions
    const ACTION_VIEW_ASSESSMENT = 'assessment_viewer';
    const ACTION_VIEW_ASSESSMENT_RESULT = 'results_viewer';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_VIEW_ASSESSMENT;

    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user, $parent)
    {
        parent :: __construct($request, $user, $parent);
        $this->register_parameters();
    }

    public function save_assessment_answer($complex_question_id, $answer, $score)
    {
        return $this->get_parent()->save_assessment_answer($complex_question_id, $answer, $score);
    }

    public function save_assessment_result($total_score)
    {
        return $this->get_parent()->save_assessment_result($total_score);
    }

    public function change_answer_data($complex_question_id, $score, $feedback)
    {
        return $this->get_parent()->change_answer_data($complex_question_id, $score, $feedback);
    }

    public function change_total_score($total_score)
    {
        return $this->get_parent()->change_total_score($total_score);
    }

    public function get_assessment_current_attempt_id()
    {
        return $this->get_parent()->get_assessment_current_attempt_id();
    }

    public function get_assessment_question_attempts()
    {
        return $this->get_parent()->get_assessment_question_attempts();
    }

    public function get_assessment_question_attempt($complex_content_object_question_id)
    {
        return $this->get_parent()->get_assessment_question_attempt($complex_content_object_question_id);
    }

    public function get_assessment_back_url()
    {
        return $this->get_parent()->get_assessment_back_url();
    }

    public function get_assessment_continue_url()
    {
        return $this->get_parent()->get_assessment_continue_url();
    }

    public function get_assessment_current_url()
    {
        return $this->get_parent()->get_assessment_current_url();
    }

    /**
     *
     * @return \core\repository\content_object\assessment\display\Configuration
     */
    public function get_configuration()
    {
        return $this->get_parent()->get_assessment_configuration();
    }

    public function register_parameters()
    {
        foreach ($this->get_parent()->get_assessment_parameters() as $parameter)
        {
            $this->set_parameter($parameter, Request :: get($parameter));
        }
    }
}
