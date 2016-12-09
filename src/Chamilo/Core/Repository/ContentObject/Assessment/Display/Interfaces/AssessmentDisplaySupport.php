<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Interfaces;

/**
 * A class implements the <code>AssessmentDisplaySupport</code> interface to indicate that it will serve as a launch
 * base for a repository\content_object\assessment\display.
 * 
 * @author Hans De Bisschop
 */
interface AssessmentDisplaySupport extends \Chamilo\Core\Repository\Display\DisplaySupport
{

    /**
     * Registers the question ids
     * 
     * @param int[] $question_ids
     */
    public function register_question_ids($question_ids);

    /**
     * Returns the registered question ids
     * 
     * @return int[] $question_ids
     */
    public function get_registered_question_ids();

    /**
     * Save the user's answer for a question
     * 
     * @param int $complex_question_id
     * @param mixed $answer
     * @param int $score
     * @param int $hint The number of times a hint was used
     */
    public function save_assessment_answer($complex_question_id, $answer, $score, $hint);

    /**
     * Write the total score to persistent storage
     * 
     * @param int $total_score
     */
    public function save_assessment_result($total_score);

    /**
     * Get the current assessment attempt id
     */
    public function get_assessment_current_attempt_id();

    /**
     * Get the question attempt trackers for all question in a specific assessment context
     * 
     * @return multitype<QuestionAttemptsTracker>
     */
    public function get_assessment_question_attempts();

    /**
     * Get the question attempt tracker for a specific question in a specific assessment context
     * 
     * @param integer $complex_question_id
     * @return QuestionAttemptsTracker
     */
    public function get_assessment_question_attempt($complex_question_id);

    /**
     * Get the url to go back to after finishing the assessment
     * 
     * @return string
     */
    public function get_assessment_back_url();

    /**
     * Get the url to continue to after finishing this assessment (Particularly useful in complex structures)
     * 
     * @return string
     */
    public function get_assessment_continue_url();

    /**
     * Get the url of the current assessment (Particularly used for relaunching the assessment)
     * 
     * @return string
     */
    public function get_assessment_current_url();

    /**
     * Get the configuration parameters for the feedback display of the assessment
     * 
     * @return Configuration
     */
    public function get_assessment_configuration();

    /**
     * Get the names of the additional parameters that need to be maintained by the assessment
     * 
     * @return array
     */
    public function get_assessment_parameters();
}
