<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Storage\DataClass\ComplexOpen;
use Chamilo\Core\Repository\Form\ComplexContentObjectItemForm;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_open_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class ComplexOpenForm extends ComplexContentObjectItemForm
{

    public function get_elements()
    {
        $elements[] = $this->createElement('checkbox', ComplexOpen::PROPERTY_VISIBLE, Translation::get('Visible'));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexOpen::PROPERTY_VISIBLE] = $cloi->get_visible();
        }
        
        return $defaults;
    }

    function update_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_visible($values[ComplexOpen::PROPERTY_VISIBLE]);
        return parent::update();
    }
}
?>