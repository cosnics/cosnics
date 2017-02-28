<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Form;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Form\ConfigurationForm;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass\ComplexAssessmentMatchTextQuestion;
use Chamilo\Core\Repository\Form\ComplexContentObjectItemForm;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package core\repository\content_object\assessment_match_text_question
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ComplexAssessmentMatchTextQuestionForm extends ComplexContentObjectItemForm
{
    const PROPERTY_RECALCULATE_WEIGHT = 'recalculate_weight';
    const PROPERTY_ANSWER_FEEDBACK_OPTION = 'answer_feedback_option';

    /**
     *
     * @see \core\repository\form\ComplexContentObjectItemForm::get_elements()
     */
    public function get_elements()
    {
        $elements = array();
        
        $elements[] = $this->createElement(
            'checkbox', 
            self::PROPERTY_RECALCULATE_WEIGHT, 
            Translation::get('RecalculateWeight'));
        
        $elements[] = $this->createElement(
            'text', 
            ComplexAssessmentMatchTextQuestion::PROPERTY_WEIGHT, 
            Translation::get('Weight'), 
            array("size" => "50", 'disabled' => 'disabled'));
        
        $elements[] = ConfigurationForm::build_answer_feedback(
            $this, 
            array(
                Configuration::ANSWER_FEEDBACK_TYPE_GIVEN, 
                Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_CORRECT, 
                Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_WRONG, 
                Configuration::ANSWER_FEEDBACK_TYPE_CORRECT, 
                Configuration::ANSWER_FEEDBACK_TYPE_WRONG, 
                Configuration::ANSWER_FEEDBACK_TYPE_ALL));
        
        return $elements;
    }

    /**
     *
     * @see \core\repository\form\ComplexContentObjectItemForm::get_default_values()
     */
    public function get_default_values()
    {
        $complex_content_object_item = $this->get_complex_content_object_item();
        
        if (isset($complex_content_object_item))
        {
            $defaults[ComplexAssessmentMatchTextQuestion::PROPERTY_WEIGHT] = $complex_content_object_item->get_weight() ? $complex_content_object_item->get_weight() : 0;

            if ($complex_content_object_item->get_show_answer_feedback() == Configuration::ANSWER_FEEDBACK_TYPE_NONE)
            {
                $defaults[self::PROPERTY_ANSWER_FEEDBACK_OPTION] = 0;
            }
            else
            {
                $defaults[self::PROPERTY_ANSWER_FEEDBACK_OPTION] = 1;
                $defaults[Configuration::PROPERTY_SHOW_ANSWER_FEEDBACK] = $complex_content_object_item->get_show_answer_feedback();
            }

            $defaults[self::PROPERTY_RECALCULATE_WEIGHT] = 1;

            if($complex_content_object_item->get_weight() > 0)
            {
                if($complex_content_object_item->get_weight() != $complex_content_object_item->get_ref_object()->get_default_weight())
                {
                    $defaults[self::PROPERTY_RECALCULATE_WEIGHT] = 0;
                }
            }
        }
        
        return $defaults;
    }

    /**
     *
     * @param string[] $values
     * @return boolean
     */
    public function update_from_values($values)
    {
        $complex_content_object_item = $this->get_complex_content_object_item();
        $this->set_values($complex_content_object_item, $values);
        
        return parent::update();
    }

    /**
     *
     * @param \core\repository\storage\data_class\ComplexContentObjectItem $complex_content_object_item
     * @param string[] $values
     */
    private function set_values($complex_content_object_item, $values)
    {
        if ($values[self::PROPERTY_RECALCULATE_WEIGHT] == 1)
        {
            $complex_content_object_item->set_weight(
                $complex_content_object_item->get_ref_object()->get_default_weight());
        }
        else
        {
            $complex_content_object_item->set_weight($values[ComplexAssessmentMatchTextQuestion::PROPERTY_WEIGHT]);
        }

        $answerFeedback = ($values[self::PROPERTY_ANSWER_FEEDBACK_OPTION] == 1) ?
            $values[ComplexAssessmentMatchTextQuestion::PROPERTY_SHOW_ANSWER_FEEDBACK] :
            Configuration::ANSWER_FEEDBACK_TYPE_NONE;

        $complex_content_object_item->set_show_answer_feedback($answerFeedback);
    }
}
