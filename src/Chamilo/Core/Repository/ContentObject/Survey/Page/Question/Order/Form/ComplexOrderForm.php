<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Storage\DataClass\ComplexOrder;
use Chamilo\Core\Repository\Form\ComplexContentObjectItemForm;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_order_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class ComplexOrderForm extends ComplexContentObjectItemForm
{

    public function get_elements()
    {
        $elements[] = $this->createElement('checkbox', ComplexOrder :: PROPERTY_VISIBLE, Translation :: get('Visible'));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexOrder :: PROPERTY_VISIBLE] = $cloi->get_visible();
        }
        
        return $defaults;
    }

    function update_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_visible($values[ComplexOrder :: PROPERTY_VISIBLE]);
        return parent :: update();
    }
}
?>