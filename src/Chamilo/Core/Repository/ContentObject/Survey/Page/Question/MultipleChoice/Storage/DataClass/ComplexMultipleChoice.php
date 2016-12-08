<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Interfaces\PageDisplayItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 *
 * @package repository.content_object.survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class ComplexMultipleChoice extends ComplexContentObjectItem implements PageDisplayItem
{
    const DATA_DISPLAY_TYPE = 'data-display-type';
    const PROPERTY_VISIBLE = 'visible';

    static function get_additional_property_names()
    {
        return array(self::PROPERTY_VISIBLE);
    }

    function get_visible()
    {
        return $this->get_additional_property(self::PROPERTY_VISIBLE);
    }

    function set_visible($value)
    {
        $this->set_additional_property(self::PROPERTY_VISIBLE, $value);
    }

    function is_visible()
    {
        return $this->get_visible() == 1;
    }

    function toggle_visibility()
    {
        $this->set_visible(! $this->get_visible());
    }

    public function getAnswerIds($prefix = null)
    {
        $answer_ids = array();
        
        if ($prefix)
        {
            $answerId = $prefix . '_' . $this->getId();
        }
        else
        {
            $answerId = $this->getId();
        }
        
        if ($this->get_ref_object()->get_display_type() == MultipleChoice::DISPLAY_TYPE_TABLE &&
             $this->get_ref_object()->get_answer_type() == MultipleChoice::ANSWER_TYPE_CHECKBOX)
        {
            foreach ($this->get_ref_object()->get_options() as $option)
            {
                $answer_ids[] = $answerId . '_' . $option->get_id();
            }
        }
        else
        {
            $answer_ids[] = $answerId;
        }
        
        return $answer_ids;
    }

    function getDataAttributes()
    {
        $attributes = array();
        $question = $this->get_ref_object();
        if ($question->get_display_type() == MultipleChoice::DISPLAY_TYPE_SELECT)
        {
            if ($question->get_answer_type() == MultipleChoice::ANSWER_TYPE_CHECKBOX)
            {
                $attributes[self::DATA_DISPLAY_TYPE] = 'mcspecial';
            }
            else
            {
                $attributes[self::DATA_DISPLAY_TYPE] = 'mcnormal';
            }
        }
        else
        {
            $attributes[self::DATA_DISPLAY_TYPE] = 'mcnormal';
        }
        
        return $attributes;
    }
}
?>