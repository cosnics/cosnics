<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 * $Id: score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc
 */
/**
 * Abstract class so each question type can determine the correct score with the given answers
 */
abstract class ScoreCalculator
{

    private $answer;

    private $question;

    public function __construct($question, $answer, $weight)
    {
        $this->answer = $answer;
        $this->question = $question;
        $this->weight = $weight;
    }

    abstract public function calculate_score();

    public function get_answer()
    {
        return $this->answer;
    }

    public function get_question()
    {
        return $this->question;
    }

    public function get_weight()
    {
        return $this->weight;
    }

    public function make_score_relative($score, $total_weight)
    {
        $relative_weight = $this->weight;
        
        if ($relative_weight == null)
            return $score;
        
        if ($total_weight == 0)
        {
            return 0;
        }
        
        $factor = ($total_weight / $relative_weight);
        
        $new_score = round(($score / $factor) * 100) / 100;
        
        return $new_score;
    }

    public static function factory($question, $answer, $weight)
    {
        $type = $question->get_type();
        
        $class = ClassnameUtilities::getInstance()->getNamespaceParent($type, 3) . '\Integration\\' .
             Assessment::package() . '\Display\ScoreCalculator';
        
        $score_calculator = new $class($question, $answer, $weight);
        return $score_calculator;
    }
}
