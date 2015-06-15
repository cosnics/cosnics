<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Interfaces\PageDisplayItem;

/**
 *
 * @package repository.content_object.survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class ComplexMultipleChoice extends ComplexContentObjectItem implements PageDisplayItem
{
    const PROPERTY_VISIBLE = 'visible';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_VISIBLE);
    }

    function get_visible()
    {
        return $this->get_additional_property(self :: PROPERTY_VISIBLE);
    }

    function set_visible($value)
    {
        $this->set_additional_property(self :: PROPERTY_VISIBLE, $value);
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
        
        if ($this->get_ref_object()->get_display_type() == MultipleChoice :: DISPLAY_TYPE_TABLE  && $this->get_ref_object()->get_answer_type() == MultipleChoice :: ANSWER_TYPE_CHECKBOX)
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
}
?>