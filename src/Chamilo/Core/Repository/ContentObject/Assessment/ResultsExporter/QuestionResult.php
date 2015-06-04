<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter;

/**
 * Class that defines the result of a question in an assessment
 * 
 * @package repository\content_object\assessment
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class QuestionResult
{

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    
    /**
     * The answer of the user
     * 
     * @var string
     */
    private $answer;

    /**
     * The assessment result to which this question result belongs to
     * 
     * @var AssessmentResult
     */
    private $assessment_result;

    /**
     * The id of the complex content object item for the question
     * 
     * @var int
     */
    private $complex_question_id;

    /**
     * The score
     * 
     * @var int
     */
    private $score;

    /**
     * Additional information columns for the export
     * 
     * @var string[string]
     *
     * @example $additional_information[column_header_id] = column_value
     */
    private $additional_information;

    /**
     * **************************************************************************************************************
     * Public functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param string $answer
     * @param AssessmentResult $assessment_result
     * @param int $complex_question_id
     * @param int $score
     * @param string[] $additional_information
     */
    function __construct($answer, $assessment_result, $complex_question_id, $score, $additional_information = array())
    {
        $this->answer = $answer;
        $this->assessment_result = $assessment_result;
        $this->complex_question_id = $complex_question_id;
        $this->score = $score;
        $this->additional_information = $additional_information;
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     *
     * @param string $answer
     */
    public function set_answer($answer)
    {
        $this->answer = $answer;
    }

    /**
     *
     * @return string
     */
    public function get_answer()
    {
        return $this->answer;
    }

    /**
     *
     * @param \core\repository\content_object\assessment\AssessmentResult $assessment_result
     */
    public function set_assessment_result($assessment_result)
    {
        $this->assessment_result = $assessment_result;
    }

    /**
     *
     * @return \core\repository\content_object\assessment\AssessmentResult
     */
    public function get_assessment_result()
    {
        return $this->assessment_result;
    }

    /**
     *
     * @param int $complex_question_id
     */
    public function set_complex_question_id($complex_question_id)
    {
        $this->complex_question_id = $complex_question_id;
    }

    /**
     *
     * @return int
     */
    public function get_complex_question_id()
    {
        return $this->complex_question_id;
    }

    /**
     *
     * @param int $score
     */
    public function set_score($score)
    {
        $this->score = $score;
    }

    /**
     *
     * @return int
     */
    public function get_score()
    {
        return $this->score;
    }

    /**
     *
     * @param string[string] $additional_information
     */
    public function set_additional_information($additional_information)
    {
        $this->additional_information = $additional_information;
    }

    /**
     *
     * @return string[string]
     */
    public function get_additional_information()
    {
        return $this->additional_information;
    }
}