<?php
namespace Chamilo\Core\Repository\ContentObject\WikiPage\Form;

use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\ComplexWikiPage;
use Chamilo\Core\Repository\Form\ComplexContentObjectItemForm;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package core\repository\content_object\wiki_page
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ComplexWikiPageForm extends ComplexContentObjectItemForm
{

    /**
     *
     * @see \core\repository\form\ComplexContentObjectItemForm::get_elements()
     */
    public function get_elements()
    {
        $elements = array();
        
        $elements[] = $this->createElement(
            'checkbox', 
            ComplexWikiPage::PROPERTY_IS_HOMEPAGE, 
            Translation::get('IsHomepage'));
        
        return $elements;
    }

    /**
     *
     * @see \core\repository\form\ComplexContentObjectItemForm::get_default_values()
     */
    public function get_default_values()
    {
        $complex_content_object_item = $this->get_complex_content_object_item();
        
        if (isset($complex_content_object_item))
        {
            $defaults[ComplexWikiPage::PROPERTY_IS_HOMEPAGE] = $complex_content_object_item->get_is_homepage() ? $complex_content_object_item->get_is_homepage() : false;
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
        $complex_content_object_item = $this->get_complex_content_object_item();
        $complex_content_object_item->set_is_homepage(
            empty($values[ComplexWikiPage::PROPERTY_IS_HOMEPAGE]) ? false : $values[ComplexWikiPage::PROPERTY_IS_HOMEPAGE]);
        return parent::update();
    }
}
