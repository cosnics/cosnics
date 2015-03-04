<?php
namespace Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\BlockRegistration;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Row;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: home_block_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * 
 * @package home.lib.forms
 */
class BlockForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';

    private $homeblock;

    private $form_type;

    public function __construct($form_type, $homeblock, $action)
    {
        parent :: __construct('home_block', 'post', $action);
        
        $this->homeblock = $homeblock;
        $this->form_type = $form_type;
        $this->build_editing_form();
        $this->setDefaults();
    }

    public function get_blocks_registrations()
    {
        $result = array();
        $registrations = DataManager :: retrieves(BlockRegistration :: class_name());
        
        while ($registration = $registrations->next_result())
        {
            $context = $registration->get_context();
            $block = $registration->get_block();
            $name = Translation :: get('TypeName', null, $context) . ' - ' . Translation :: get(
                (string) StringUtilities :: getInstance()->createString($block)->upperCamelize(), 
                null, 
                $context);
            $result[$registration->get_id()] = $name;
        }
        return $result;
    }

    public function build_basic_form()
    {
        $this->addElement('text', Block :: PROPERTY_TITLE, Translation :: get('BlockTitle'), array("size" => "50"));
        $this->addRule(
            Block :: PROPERTY_TITLE, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        /*
         * $this->addElement('select', Block :: PROPERTY_, Translation :: get('BlockColumn'), $this->get_columns());
         * $this->addRule(Block :: PROPERTY_COLUMN, Translation :: get('ThisFieldIsRequired', null, Utilities ::
         * COMMON_LIBRARIES), 'required');
         */
        
        $this->addElement('select', Block :: PROPERTY_COLUMN, Translation :: get('BlockColumn'), $this->get_columns());
        $this->addRule(
            Block :: PROPERTY_COLUMN, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement(
            'select', 
            Block :: PROPERTY_REGISTRATION_ID, 
            Translation :: get('BlockComponent'), 
            $this->get_blocks_registrations());
        $this->addRule(
            Block :: PROPERTY_REGISTRATION_ID, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('hidden', Block :: PROPERTY_USER);
    }

    public function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', Block :: PROPERTY_ID);
        
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
        $homeblock = $this->homeblock;
        $values = $this->exportValues();
        
        if ($homeblock->get_registration->get_id() != $values[Block :: PROPERTY_REGISTRATION_ID])
        {
            if (! DataManager :: delete_home_block_configs($homeblock))
            {
                return false;
            }
            if (! $homeblock->create_initial_settings())
            {
                return false;
            }
        }
        
        $homeblock->set_title($values[Block :: PROPERTY_TITLE]);
        $homeblock->set_column($values[Block :: PROPERTY_COLUMN]);
        $homeblock->set_registration_id($values[Block :: PROPERTY_REGISTRATION_ID]);
        
        return $homeblock->update();
    }

    public function get_columns()
    {
        $user_id = $this->homeblock->get_user();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_USER), 
            new StaticConditionVariable($user_id));
        $order_by = array();
        $order_by[] = new OrderBy(new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_SORT));
        
        $parameters = new DataClassRetrievesParameters($condition, null, null, $order_by);
        $columns = DataManager :: retrieves(Column :: class_name(), $parameters);
        $column_options = array();
        while ($column = $columns->next_result())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Row :: class_name(), Row :: PROPERTY_ID), 
                new StaticConditionVariable($column->get_row()));
            
            $condition = new SubselectCondition(
                new PropertyConditionVariable(Tab :: class_name(), Tab :: PROPERTY_ID), 
                new PropertyConditionVariable(Row :: class_name(), Row :: PROPERTY_TAB), 
                null, 
                $condition);
            
            $order_by = array();
            $order_by[] = new OrderBy(new PropertyConditionVariable(Tab :: class_name(), Tab :: PROPERTY_SORT));
            
            $parameters = new DataClassRetrievesParameters($condition, null, null, $order_by);
            
            $tab = DataManager :: retrieves(Tab :: class_name(), $parameters)->next_result();
            
            if ($tab)
                $name = Translation :: get('Tab') . ' ' . $tab->get_title() . ' :';
            
            $column_options[$column->get_id()] = $name . $column->get_title();
        }
        
        return $column_options;
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
        $homeblock = $this->homeblock;
        $defaults[Block :: PROPERTY_ID] = $homeblock->get_id();
        $defaults[Block :: PROPERTY_TITLE] = $homeblock->get_title();
        $defaults[Block :: PROPERTY_COLUMN] = $homeblock->get_column();
        $defaults[Block :: PROPERTY_REGISTRATION_ID] = $homeblock->get_registration_id();
        $defaults[Block :: PROPERTY_USER] = $homeblock->get_user();
        parent :: setDefaults($defaults);
    }
}
