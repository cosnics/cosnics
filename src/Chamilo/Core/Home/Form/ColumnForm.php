<?php
namespace Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Row;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: home_column_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * 
 * @package home.lib.forms
 */
class ColumnForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';

    private $homecolumn;

    private $form_type;

    public function __construct($form_type, $homecolumn, $action)
    {
        parent :: __construct('home_column', 'post', $action);
        
        $this->homecolumn = $homecolumn;
        $this->form_type = $form_type;
        $this->build_editing_form();
        
        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement('text', Column :: PROPERTY_TITLE, Translation :: get('ColumnTitle'), array("size" => "50"));
        $this->addRule(
            Column :: PROPERTY_TITLE, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('select', Column :: PROPERTY_ROW, Translation :: get('ColumnRow'), $this->get_rows());
        $this->addRule(
            Column :: PROPERTY_ROW, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('text', Column :: PROPERTY_WIDTH, Translation :: get('ColumnWidth'), array("size" => "50"));
        $this->addRule(
            Column :: PROPERTY_WIDTH, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('hidden', Column :: PROPERTY_USER);
    }

    public function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', Column :: PROPERTY_ID);
        $this->addRule(
            Column :: PROPERTY_WIDTH, 
            Translation :: get('MaxColumnWidthValue'), 
            'max_value', 
            $this->exportValues());
        
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
        $homecolumn = $this->homecolumn;
        $values = $this->exportValues();
        
        $homecolumn->set_title($values[Column :: PROPERTY_TITLE]);
        $homecolumn->set_row($values[Column :: PROPERTY_ROW]);
        $homecolumn->set_width($values[Column :: PROPERTY_WIDTH]);
        
        return $homecolumn->update();
    }

    public function create_object()
    {
        $homecolumn = $this->homecolumn;
        $values = $this->exportValues();
        
        $homecolumn->set_title($values[Column :: PROPERTY_TITLE]);
        $homecolumn->set_row($values[Column :: PROPERTY_ROW]);
        $homecolumn->set_width($values[Column :: PROPERTY_WIDTH]);
        
        return $homecolumn->create();
    }

    public function get_rows()
    {
        $user_id = $this->homecolumn->get_user();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Row :: class_name(), Row :: PROPERTY_USER), 
            new StaticConditionVariable($user_id));
        
        $rows = DataManager :: retrieves(Row :: class_name(), $condition);
        $row_options = array();
        while ($row = $rows->next_result())
        {
            $row_options[$row->get_id()] = $row->get_title();
        }
        
        return $row_options;
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     * 
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $homecolumn = $this->homecolumn;
        $defaults[Column :: PROPERTY_ID] = $homecolumn->get_id();
        $defaults[Column :: PROPERTY_TITLE] = $homecolumn->get_title();
        $defaults[Column :: PROPERTY_ROW] = $homecolumn->get_row();
        $defaults[Column :: PROPERTY_WIDTH] = $homecolumn->get_width();
        $defaults[Column :: PROPERTY_USER] = $homecolumn->get_user();
        
        parent :: setDefaults($defaults);
    }
}
