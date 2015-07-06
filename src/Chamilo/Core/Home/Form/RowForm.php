<?php
namespace Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Storage\DataClass\Row;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: home_row_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * 
 * @package home.lib.forms
 */
class RowForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';

    private $homerow;

    private $form_type;

    public function __construct($form_type, $homerow, $action)
    {
        parent :: __construct('home_row', 'post', $action);
        
        $this->homerow = $homerow;
        $this->form_type = $form_type;
        $this->build_editing_form();
        
        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement('text', Row :: PROPERTY_TITLE, Translation :: get('RowTitle'), array("size" => "50"));
        $this->addRule(
            Row :: PROPERTY_TITLE, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('select', Row :: PROPERTY_TAB, Translation :: get('RowTab'), $this->get_tabs());
        $this->addRule(
            Row :: PROPERTY_TAB, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('hidden', Row :: PROPERTY_USER);
        
        // $this->addElement('submit', 'home_row', Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES));
    }

    public function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', Row :: PROPERTY_ID);
        
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
        $homerow = $this->homerow;
        $values = $this->exportValues();
        
        $homerow->set_title($values[Row :: PROPERTY_TITLE]);
        $homerow->set_tab($values[Row :: PROPERTY_TAB]);
        
        return $homerow->update();
    }

    public function get_tabs()
    {
        $user_id = $this->homerow->get_user();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Tab :: class_name(), Tab :: PROPERTY_USER), 
            new StaticConditionVariable($user_id));
        
        $tabs = DataManager :: retrieves(Tab :: class_name(), $condition);
        $tab_options = array();
        while ($tab = $tabs->next_result())
        {
            $tab_options[$tab->get_id()] = $tab->get_title();
        }
        
        return $tab_options;
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
        $homerow = $this->homerow;
        $defaults[Row :: PROPERTY_ID] = $homerow->get_id();
        $defaults[Row :: PROPERTY_TITLE] = $homerow->get_title();
        $defaults[Row :: PROPERTY_TAB] = $homerow->get_tab();
        $defaults[Row :: PROPERTY_USER] = $homerow->get_user();
        parent :: setDefaults($defaults);
    }
}
