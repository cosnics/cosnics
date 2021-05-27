<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestion;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestionAnswer;
use Chamilo\Libraries\Translation\Translation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        
        // get answer text
        $answer_text = $object->get_answer_text();
        
        // get the regular text
        $clear_text = preg_split(FillInBlanksQuestionAnswer::CLOZE_REGEX, $answer_text);
        
        // get the answers
        $answers = $object->get_answers();
        
        // get the number of questions
        $questions = $object->get_number_of_questions();
        
        // position the answers
        $question_answers = [];
        foreach ($answers as $answer)
        {
            $question_answers[$answer->get_position()][] = $answer;
        }
        
        // construct HTML
        $html = [];
        
        // default scores
        $html[] = '<br/><b>' . Translation::get('DefaultScores') . ':</b><br/>';
        $html[] = $object->get_default_positive_score();
        $html[] = ' / ';
        $html[] = $object->get_default_negative_score();
        $html[] = '<br/><br/>';
        
        // answer text
        $html[] = '<b>' . Translation::get('Excercise') . ':</b><br/>';
        
        for ($i = 0; $i < $questions; $i ++)
        {
            // regular text $i
            $html[] = $clear_text[$i];
            
            // answer $i
            if ($object->get_question_type() == FillInBlanksQuestion::TYPE_SELECT)
            {
                // combobox
                $answer_select = [];
                $answer_select[] = '<select name="answer">';
                foreach ($question_answers[$i] as $answer)
                {
                    $value = trim($answer->get_value());
                    $answer_select[] = '<option value="' . $value . '">' . $value . '</option>';
                }
                $answer_select[] = '</select>';
                
                $html[] = implode(PHP_EOL, $answer_select);
            }
            else
            {
                // uniform inputfield
                $size = $object->get_input_field_size($i);
                $best = $object->get_best_answer_for_question($i);
                $html[] = '<input class="' . FillInBlanksQuestion::TEXT_INPUT_FIELD_CSS_CLASS . '" size="' . $size .
                     '" title="' . Translation::get('BestAnswer') . '" value="' . $best->get_value() . '" />';
            }
        }
        
        $array_size = count($clear_text);
        while ($array_size > $i)
        {
            $html[] = $clear_text[$i];
            $i ++;
        }
        
        return implode(PHP_EOL, $html);
    }
}
