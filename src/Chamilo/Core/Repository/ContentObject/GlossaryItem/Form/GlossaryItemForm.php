<?php
namespace Chamilo\Core\Repository\ContentObject\GlossaryItem\Form;

use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Core\Repository\Form\ContentObjectForm;

/**
 * $Id: glossary_item_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.glossary_item
 */
/**
 * This class represents a form to create or update glossary_items
 */
class GlossaryItemForm extends ContentObjectForm
{
    
    // Inherited
    public function create_content_object()
    {
        $object = new GlossaryItem();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

/**
 * Builds a form to create or edit a learning object.
 * Creates fields for default learning object properties. The result
 * of this function is equal to build_creation_form()'s, but that one may be overridden to extend the form.
 */
    /*
     * public function build_basic_form($htmleditor_options = array()) { $this->addElement('html', '<div
     * id="message"></div>'); $this->add_textfield(ContentObject :: PROPERTY_TITLE, Translation :: get('Word', array(),
     * ClassnameUtilities :: getInstance()->getNamespaceFromObject($this)), true, array( 'size' => '100', 'id' =>
     * 'title', 'style' => 'width: 95%')); if ($this->allows_category_selection()) { $select =
     * $this->add_select(ContentObject :: PROPERTY_PARENT_ID, Translation :: get('CategoryTypeName'),
     * $this->get_categories()); $select->setSelected($this->get_content_object()->get_parent_id()); } $required = true;
     * $htmleditor_options = array(); $htmleditor_options['height'] = '50'; $htmleditor_options['collapse_toolbar'] =
     * true; $this->add_html_editor(ContentObject :: PROPERTY_DESCRIPTION, Translation :: get('Definition'), $required,
     * $htmleditor_options); } protected function build_creation_form($htmleditor_options = array()) {
     * $this->addElement('category', Translation :: get('GeneralProperties'));
     * $this->build_basic_form($htmleditor_options); $this->addElement('category'); }
     */
}
