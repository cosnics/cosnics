<?php
namespace Chamilo\Core\Help\Form;

use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: help_item_form.class.php 226 2009-11-13 14:44:03Z chellee $
 * 
 * @package help.lib.forms
 */
class HelpItemForm extends FormValidator
{

    private $help_item;

    public function __construct($help_item, $action)
    {
        parent::__construct('help_item', 'post', $action);
        
        $this->help_item = $help_item;
        $this->build_basic_form();
        
        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement(
            'text', 
            HelpItem::PROPERTY_URL, 
            Translation::get('URL', null, Utilities::COMMON_LIBRARIES), 
            array('size' => '100'));
        $this->addRule(
            HelpItem::PROPERTY_URL, 
            Translation::get('Required', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_help_item()
    {
        $help_item = $this->help_item;
        $values = $this->exportValues();
        
        $help_item->set_url($values[HelpItem::PROPERTY_URL]);
        
        return $help_item->update();
    }

    /**
     * Sets default values.
     * 
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $help_item = $this->help_item;
        $defaults[HelpItem::PROPERTY_URL] = $help_item->get_url();
        parent::setDefaults($defaults);
    }

    public function get_help_item()
    {
        return $this->help_item;
    }
}
