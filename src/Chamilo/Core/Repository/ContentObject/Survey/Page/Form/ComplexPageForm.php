<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\ComplexPage;
use Chamilo\Core\Repository\Form\ComplexContentObjectItemForm;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class ComplexPageForm extends ComplexContentObjectItemForm
{

    public function get_elements()
    {
        $elements[] = $this->createElement('checkbox', ComplexPage::PROPERTY_VISIBLE, Translation::get('Visible'));
        return $elements;
    }

    function get_default_values()
    {
        $cloi = $this->get_complex_content_object_item();
        
        if (isset($cloi))
        {
            $defaults[ComplexPage::PROPERTY_VISIBLE] = $cloi->get_visible();
        }
        
        return $defaults;
    }

    function update_from_values($values)
    {
        $cloi = $this->get_complex_content_object_item();
        $cloi->set_visible($values[ComplexPage::PROPERTY_VISIBLE]);
        return parent::update();
    }
}

?>