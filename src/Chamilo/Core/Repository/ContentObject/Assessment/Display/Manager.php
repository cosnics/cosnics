<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package repository.lib.complex_display.assessment
 */

/**
 * This tool allows a user to publish assessments in his or her course.
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    // Actions
    const ACTION_VIEW_ASSESSMENT = 'AssessmentViewer';
    const ACTION_VIEW_ASSESSMENT_RESULT = 'ResultsViewer';

    // Default action
    const DEFAULT_ACTION = self::ACTION_VIEW_ASSESSMENT;

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
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
     * @return Configuration
     */
    public function get_configuration()
    {
        return $this->get_parent()->get_assessment_configuration();
    }

    public function register_parameters()
    {
        foreach ($this->get_parent()->get_assessment_parameters() as $parameter)
        {
            $this->set_parameter($parameter, Request::get($parameter));
        }
    }
}
