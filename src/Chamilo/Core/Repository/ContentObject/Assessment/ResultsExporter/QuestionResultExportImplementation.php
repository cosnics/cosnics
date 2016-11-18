<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 * Abstract class that defines the implementation of the export of a question result in the assessment result exporter
 * 
 * @package repository\content_object\assessment
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class QuestionResultExportImplementation
{

    /**
     * **************************************************************************************************************
     * COLUMN DEFINITIONS *
     * **************************************************************************************************************
     */
    
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    
    /**
     * The controller
     * 
     * @var AssessmentResultsExportController
     */
    private $controller;

    /**
     * The complex content object item of the question
     * 
     * @var ComplexContentObjectItem
     */
    private $complex_question;

    /**
     * The result of the question
     * 
     * @var QuestionResult
     */
    private $question_result;

    /**
     * **************************************************************************************************************
     * Public functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Constructs this class
     * 
     * @param ComplexContentObjectItem $complex_question
     * @param AssessmentResultsExportController $controller
     * @param $question_result
     */
    public function __construct($complex_question, $controller, $question_result)
    {
        $this->complex_question = $complex_question;
        $this->controller = $controller;
        $this->question_result = $question_result;
    }

    /**
     * Factory to determine the extension of this class
     * 
     * @param ComplexContentObjectItem $complex_question
     * @param AssessmentResultsExportController $controller
     * @param QuestionResult $question_result
     */
    public static function factory($complex_question, $controller, $question_result)
    {
        $namespace = ClassnameUtilities::getInstance()->getNamespaceFromObject($complex_question);
        $class = $namespace . '\\QuestionResultExportImplementation';
        
        if (! class_exists($class, true))
        {
            $class = __NAMESPACE__ . '\\DefaultQuestionResultExportImplementation';
        }
        
        return new $class($complex_question, $controller, $question_result);
    }

    /**
     * Initializes and runs this export implementation
     * 
     * @param ComplexContentObjectItem $complex_question
     * @param AssessmentResultsExportController $controller
     * @param QuestionResult $question_result
     */
    public static function launch($complex_question, $controller, $question_result)
    {
        $instance = self::factory($complex_question, $controller, $question_result);
        $instance->run();
    }

    /**
     * **************************************************************************************************************
     * Abstract functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Runs this exporter
     */
    abstract public function run();

    /**
     * **************************************************************************************************************
     * Protected functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Adds the answer to the export
     * 
     * @param $answer
     */
    protected function add_answer_to_export($answer)
    {
        if (is_array($answer))
        {
            $answer = implode('<br />', $answer);
        }
        
        $this->get_controller()->add_data_to_current_row(
            AssessmentResultsExportController::COLUMN_ATTEMPT_ANSWER, 
            $answer);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     *
     * @param \core\repository\storage\data_class\ComplexContentObjectItem $complex_question
     */
    public function set_complex_question($complex_question)
    {
        $this->complex_question = $complex_question;
    }

    /**
     *
     * @return \core\repository\storage\data_class\ComplexContentObjectItem
     */
    public function get_complex_question()
    {
        return $this->complex_question;
    }

    /**
     *
     * @param \core\repository\content_object\assessment\AssessmentResultsExportController $controller
     */
    public function set_controller($controller)
    {
        $this->controller = $controller;
    }

    /**
     *
     * @return \core\repository\content_object\assessment\AssessmentResultsExportController
     */
    public function get_controller()
    {
        return $this->controller;
    }

    /**
     *
     * @param \core\repository\content_object\assessment\QuestionResult $question_result
     */
    public function set_question_result($question_result)
    {
        $this->question_result = $question_result;
    }

    /**
     *
     * @return \core\repository\content_object\assessment\QuestionResult
     */
    public function get_question_result()
    {
        return $this->question_result;
    }
}