<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Storage\DataClass\ComplexChoice;
use Chamilo\Core\Repository\Form\ComplexContentObjectItemForm;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_date_time_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class ComplexChoiceForm extends ComplexContentObjectItemForm
{

    public function get_elements()
    {
        $elements[] = $this->createElement('checkbox', ComplexChoice::PROPERTY_VISIBLE, Translation::get('Visible'));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexChoice::PROPERTY_VISIBLE] = $cloi->get_visible();
        }
        
        return $defaults;
    }

    function update_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_visible($values[ComplexChoice::PROPERTY_VISIBLE]);
        return parent::update();
    }
}

?>