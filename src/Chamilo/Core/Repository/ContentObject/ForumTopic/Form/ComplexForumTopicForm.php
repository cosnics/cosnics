<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Form;

use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ComplexForumTopic;
use Chamilo\Core\Repository\Form\ComplexContentObjectItemForm;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\content_object\forum_topic
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ComplexForumTopicForm extends ComplexContentObjectItemForm
{

    /**
     *
     * @see \core\repository\form\ComplexContentObjectItemForm::get_elements()
     */
    public function get_elements()
    {
        $elements = array();
        
        $elements[] = $this->createElement(
            'radio', 
            ComplexForumTopic::PROPERTY_FORUM_TYPE, 
            Translation::get('Nothing', null, Utilities::COMMON_LIBRARIES), 
            '', 
            0);
        $elements[] = $this->createElement(
            'radio', 
            ComplexForumTopic::PROPERTY_FORUM_TYPE, 
            Translation::get('Sticky', null, 'Chamilo\Core\Repository\ContentObject\Forum'), 
            '', 
            1);
        $elements[] = $this->createElement(
            'radio', 
            ComplexForumTopic::PROPERTY_FORUM_TYPE, 
            Translation::get('Important', null, 'Chamilo\Core\Repository\ContentObject\Forum'), 
            '', 
            2);
        
        return $elements;
    }

    /**
     *
     * @see \core\repository\form\ComplexContentObjectItemForm::get_default_values()
     */
    public function get_default_values($defaults = array ())
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexForumTopic::PROPERTY_FORUM_TYPE] = $cloi->get_forum_type() ? $cloi->get_forum_type() : 0;
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
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_forum_type($values[ComplexForumTopic::PROPERTY_FORUM_TYPE]);
        return parent::update();
    }
}
