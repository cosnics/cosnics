<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Interfaces\PageDisplayItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

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
        $content_object = $this->get_ref_object();
        
        if ($prefix)
        {
            $answerId = $prefix . '_' . $this->get_id();
        }
        else
        {
            $answerId = $this->get_id();
        }
        
        foreach ($content_object->get_options() as $option)
        {
            
            if ($content_object->get_matrix_type() == Matrix::MATRIX_TYPE_CHECKBOX)
            {
                foreach ($content_object->get_matches() as $match)
                {
                    $answer_ids[] = $answerId . '_' . $option->get_id() . '_' . $match->get_id();
                }
            }
            else
            {
                $answer_ids[] = $answerId . '_' . $option->get_id();
            }
        }
        
        return $answer_ids;
    }

    function getDataAttributes()
    {
        return null;
    }
}