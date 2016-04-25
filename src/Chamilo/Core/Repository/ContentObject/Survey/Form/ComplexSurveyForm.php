<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass\ComplexSurvey;
use Chamilo\Core\Repository\Form\ComplexContentObjectItemForm;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: complex_survey_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.survey
 */
/**
 * This class represents a form to create or update complex surveys
 */
class ComplexSurveyForm extends ComplexContentObjectItemForm
{
    public function get_elements()
    {
        $elements[] = $this->createElement('checkbox', ComplexSurvey :: PROPERTY_VISIBLE, Translation :: get('Visible'));
        return $elements;
    }
    
    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();
    
        if (isset($cloi))
        {
            $defaults[ComplexSurvey :: PROPERTY_VISIBLE] = $cloi->get_visible();
        }
    
        return $defaults;
    }
    
    function update_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_visible($values[ComplexSurvey :: PROPERTY_VISIBLE]);
        return parent :: update();
    }
}
?>