<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Storage\DataClass\ComplexRating;
use Chamilo\Core\Repository\Form\ComplexContentObjectItemForm;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_rating_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents a complex question
 */
class ComplexRatingForm extends ComplexContentObjectItemForm
{

    public function get_elements()
    {
        $elements[] = $this->createElement('checkbox', ComplexRating::PROPERTY_VISIBLE, Translation::get('Visible'));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexRating::PROPERTY_VISIBLE] = $cloi->get_visible();
        }
        
        return $defaults;
    }

    function update_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_visible($values[ComplexRating::PROPERTY_VISIBLE]);
        return parent::update();
    }
}
?>