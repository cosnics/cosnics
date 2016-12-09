<?php
namespace Chamilo\Core\Group\Form;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Menu\GroupMenu;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: group_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * 
 * @package groups.lib.forms
 */
class GroupForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'GroupUpdated';
    const RESULT_ERROR = 'GroupUpdateFailed';

    private $parent;

    private $group;

    private $unencryptedpass;

    private $user;

    public function __construct($form_type, $group, $action, $user)
    {
        parent::__construct('groups_settings', 'post', $action);
        
        $this->group = $group;
        $this->user = $user;
        $this->form_type = $form_type;
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $this->addElement('text', Group::PROPERTY_NAME, Translation::get('Name'), array("size" => "50"));
        $this->addRule(
            Group::PROPERTY_NAME, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('text', Group::PROPERTY_CODE, Translation::get('Code'), array("size" => "50"));
        $this->addRule(
            Group::PROPERTY_CODE, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement('select', Group::PROPERTY_PARENT_ID, Translation::get('Location'), $this->get_groups());
        $this->addRule(
            Group::PROPERTY_PARENT_ID, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        // Disk Quota
        $this->addElement('text', Group::PROPERTY_DISK_QUOTA, Translation::get('DiskQuota'), array("size" => "50"));
        $this->addRule(
            Group::PROPERTY_DISK_QUOTA, 
            Translation::get('ThisFieldMustBeNumeric', null, Utilities::COMMON_LIBRARIES), 
            'numeric', 
            null, 
            'server');
        // Database Quota
        $this->addElement(
            'text', 
            Group::PROPERTY_DATABASE_QUOTA, 
            Translation::get('DatabaseQuota'), 
            array("size" => "50"));
        $this->addRule(
            Group::PROPERTY_DATABASE_QUOTA, 
            Translation::get('ThisFieldMustBeNumeric', null, Utilities::COMMON_LIBRARIES), 
            'numeric', 
            null, 
            'server');
        
        $this->add_html_editor(
            Group::PROPERTY_DESCRIPTION, 
            Translation::get('Description', null, Utilities::COMMON_LIBRARIES), 
            false);
    }

    public function build_editing_form()
    {
        $group = $this->group;
        $parent = $this->parent;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', Group::PROPERTY_ID);
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Update', null, Utilities::COMMON_LIBRARIES), 
            null, 
            null, 
            'arrow-right');
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function update_group()
    {
        $group = $this->group;
        $values = $this->exportValues();
        
        $group->set_name($values[Group::PROPERTY_NAME]);
        $group->set_description($values[Group::PROPERTY_DESCRIPTION]);
        $group->set_code($values[Group::PROPERTY_CODE]);
        $group->set_database_quota(intval($values[Group::PROPERTY_DATABASE_QUOTA]));
        $group->set_disk_quota(intval($values[Group::PROPERTY_DISK_QUOTA]));
        $value = $group->update();
        
        $new_parent = $values[Group::PROPERTY_PARENT_ID];
        if ($group->get_parent() != $new_parent)
        {
            $group->move($new_parent);
        }
        
        if ($value)
        {
            Event::trigger(
                'Update', 
                Manager::context(), 
                array(
                    \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change::PROPERTY_REFERENCE_ID => $group->get_id(), 
                    \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change::PROPERTY_USER_ID => $this->user->get_id()));
        }
        
        return $value;
    }

    public function create_group()
    {
        $group = $this->group;
        $values = $this->exportValues();
        
        $group->set_name($values[Group::PROPERTY_NAME]);
        $group->set_description($values[Group::PROPERTY_DESCRIPTION]);
        $group->set_code($values[Group::PROPERTY_CODE]);
        $group->set_parent($values[Group::PROPERTY_PARENT_ID]);
        if ($values[Group::PROPERTY_DATABASE_QUOTA] != '')
            $group->set_database_quota(intval($values[Group::PROPERTY_DATABASE_QUOTA]));
        if ($values[Group::PROPERTY_DISK_QUOTA] != '')
            $group->set_disk_quota(intval($values[Group::PROPERTY_DISK_QUOTA]));
        
        $value = $group->create();
        
        if ($value)
        {
            Event::trigger(
                'Create', 
                Manager::context(), 
                array(
                    \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change::PROPERTY_REFERENCE_ID => $group->get_id(), 
                    \Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change::PROPERTY_USER_ID => $this->user->get_id()));
        }
        
        return $value;
    }

    /**
     * Sets default values.
     * 
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $group = $this->group;
        $defaults[Group::PROPERTY_ID] = $group->get_id();
        $defaults[Group::PROPERTY_PARENT_ID] = $group->get_parent();
        $defaults[Group::PROPERTY_NAME] = $group->get_name();
        $defaults[Group::PROPERTY_CODE] = $group->get_code();
        $defaults[Group::PROPERTY_DESCRIPTION] = $group->get_description();
        $defaults[Group::PROPERTY_DATABASE_QUOTA] = $group->get_database_quota();
        $defaults[Group::PROPERTY_DISK_QUOTA] = $group->get_disk_quota();
        parent::setDefaults($defaults);
    }

    public function get_group()
    {
        return $this->group;
    }

    public function get_groups()
    {
        $group = $this->group;
        
        $group_menu = new GroupMenu($group->get_id(), null, true, true, true);
        $renderer = new OptionsMenuRenderer();
        $group_menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }
}
