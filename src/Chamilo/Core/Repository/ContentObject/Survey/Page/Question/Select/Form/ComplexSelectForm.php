<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataClass\ComplexSelect;
use Chamilo\Core\Repository\Form\ComplexContentObjectItemForm;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_select_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents a form to create or update complex surveys
 */
class ComplexSelectForm extends ComplexContentObjectItemForm
{

    public function get_elements()
    {
        $elements[] = $this->createElement('checkbox', ComplexSelect :: PROPERTY_VISIBLE, Translation :: get('Visible'));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexSelect :: PROPERTY_VISIBLE] = $cloi->get_visible();
        }
        
        return $defaults;
    }

    function update_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_visible($values[ComplexSelect :: PROPERTY_VISIBLE]);
        return parent :: update();
    }
}
?>