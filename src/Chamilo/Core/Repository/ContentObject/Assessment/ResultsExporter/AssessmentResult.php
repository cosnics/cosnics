<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter;

/**
 * Class that defines the result of an assessment
 * 
 * @package repository\content_object\assessment
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentResult
{

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    
    /**
     * A unique identifier for the result
     * 
     * @var int
     */
    private $result_id;

    /**
     * The assessment id
     * 
     * @var int
     */
    private $assessment_id;

    /**
     * The user id
     * 
     * @var int
     */
    private $user_id;

    /**
     * The start time
     * 
     * @var int
     */
    private $start_time;

    /**
     * The end time
     * 
     * @var int
     */
    private $end_time;

    /**
     * The total time
     * 
     * @var int
     */
    private $total_time;

    /**
     * The total score
     * 
     * @var int
     */
    private $total_score;

    /**
     * The question results
     * 
     * @var QuestionResult[]
     */
    private $question_results;

    /**
     * **************************************************************************************************************
     * Public functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param int $result_id
     * @param int $assessment_id
     * @param int $end_time
     * @param QuestionResult[] $question_results
     * @param int $start_time
     * @param int $total_score
     * @param int $total_time
     * @param int $user_id
     */
    function __construct($result_id, $assessment_id, $end_time, $question_results, $start_time, $total_score, 
        $total_time, $user_id)
    {
        $this->result_id = $result_id;
        $this->assessment_id = $assessment_id;
        $this->end_time = $end_time;
        $this->question_results = $question_results;
        $this->start_time = $start_time;
        $this->total_score = $total_score;
        $this->total_time = $total_time;
        $this->user_id = $user_id;
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     *
     * @param int $result_id
     */
    public function set_result_id($result_id)
    {
        $this->result_id = $result_id;
    }

    /**
     *
     * @return int
     */
    public function get_result_id()
    {
        return $this->result_id;
    }

    /**
     *
     * @param int $assessment_id
     */
    public function set_assessment_id($assessment_id)
    {
        $this->assessment_id = $assessment_id;
    }

    /**
     *
     * @return int
     */
    public function get_assessment_id()
    {
        return $this->assessment_id;
    }

    /**
     *
     * @param int $end_time
     */
    public function set_end_time($end_time)
    {
        $this->end_time = $end_time;
    }

    /**
     *
     * @return int
     */
    public function get_end_time()
    {
        return $this->end_time;
    }

    public function set_question_results($question_results)
    {
        $this->question_results = $question_results;
    }

    public function get_question_results()
    {
        return $this->question_results;
    }

    /**
     *
     * @param int $start_time
     */
    public function set_start_time($start_time)
    {
        $this->start_time = $start_time;
    }

    /**
     *
     * @return int
     */
    public function get_start_time()
    {
        return $this->start_time;
    }

    /**
     *
     * @param int $total_score
     */
    public function set_total_score($total_score)
    {
        $this->total_score = $total_score;
    }

    /**
     *
     * @return int
     */
    public function get_total_score()
    {
        return $this->total_score;
    }

    /**
     *
     * @param int $total_time
     */
    public function set_total_time($total_time)
    {
        $this->total_time = $total_time;
    }

    /**
     *
     * @return int
     */
    public function get_total_time()
    {
        return $this->total_time;
    }

    /**
     *
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     *
     * @return int
     */
    public function get_user_id()
    {
        return $this->user_id;
    }
}