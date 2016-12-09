<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Interfaces\PageDisplayItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 *
 * @package repository.content_object.survey_matching_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class ComplexMatching extends ComplexContentObjectItem implements PageDisplayItem
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
        
        foreach ($this->get_ref_object()->get_options()->as_array() as $option)
        {
            if ($prefix)
            {
                $answerId = $prefix . '_' . $this->getId();
            }
            else
            {
                $answerId = $this->getId();
            }
            
            $answer_ids[] = $answerId . '_' . $option->get_id();
        }

        return $answer_ids;
    }
    
    function getDataAttributes()
    {
        return null;
    }
}
?>