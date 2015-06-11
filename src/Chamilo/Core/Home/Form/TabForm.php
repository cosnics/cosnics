<?php
namespace Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: home_tab_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * 
 * @package home.lib.forms
 */
class TabForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';

    private $tab;

    private $form_type;

    public function __construct($form_type, $tab, $action)
    {
        parent :: __construct('home_tab', 'post', $action);
        
        $this->Tab = $tab;
        $this->form_type = $form_type;
        $this->build_editing_form();
        
        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement('text', Tab :: PROPERTY_TITLE, Translation :: get('TabTitle'), array("size" => "50"));
        $this->addRule(
            Tab :: PROPERTY_TITLE, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('hidden', Tab :: PROPERTY_USER);
        
        // $this->addElement('submit', 'home_tab', Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES));
    }

    public function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', Tab :: PROPERTY_ID);
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'positive update'));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_object()
    {
        $tab = $this->Tab;
        $values = $this->exportValues();
        
        $tab->set_title($values[Tab :: PROPERTY_TITLE]);
        
        return $tab->update();
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     * 
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $tab = $this->Tab;
        $defaults[Tab :: PROPERTY_ID] = $tab->get_id();
        $defaults[Tab :: PROPERTY_TITLE] = $tab->get_title();
        $defaults[Tab :: PROPERTY_USER] = $tab->get_user();
        parent :: setDefaults($defaults);
    }
}
