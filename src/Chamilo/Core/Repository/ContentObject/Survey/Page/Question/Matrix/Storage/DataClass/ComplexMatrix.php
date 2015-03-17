<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Interfaces\PageDisplayItem;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class ComplexMatrix extends ComplexContentObjectItem implements PageDisplayItem
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

    public function get_answer_ids()
    {
        $answer_ids = array();
        $content_object = $this->get_ref_object();
        
        foreach ($content_object->get_options() as $option)
        {
            if ($content_object->get_matrix_type() == self :: MATRIX_TYPE_CHECKBOX)
            {
                foreach ($content_object->get_matches() as $match)
                {
                    $answer_ids[] = $this->get_id() . '_' . $option->get_id() . '_' . $match->get_id();
                }
            }
            else
            {
                $answer_ids[] = $this->get_id() . '_' . $option->get_id();
            }
        }
        
        return $answer_ids;
    }
}