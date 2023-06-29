<?php
namespace Chamilo\Core\Group\Form;

use Chamilo\Core\Group\Integration\Chamilo\Core\Tracking\Storage\DataClass\Change;
use Chamilo\Core\Group\Menu\GroupMenu;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package groups.lib.forms
 */
class GroupForm extends FormValidator
{
    public const RESULT_ERROR = 'GroupUpdateFailed';

    public const RESULT_SUCCESS = 'GroupUpdated';

    public const TYPE_CREATE = 1;

    public const TYPE_EDIT = 2;

    private $group;

    private $parent;

    private $unencryptedpass;

    private $user;

    public function __construct($form_type, $group, $action, $user)
    {
        parent::__construct('groups_settings', self::FORM_METHOD_POST, $action);

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
        $this->addElement('text', Group::PROPERTY_NAME, Translation::get('Name'), ['size' => '50']);
        $this->addRule(
            Group::PROPERTY_NAME, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
        );

        $this->addElement('text', Group::PROPERTY_CODE, Translation::get('Code'), ['size' => '50']);
        $this->addRule(
            Group::PROPERTY_CODE, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required'
        );

        $this->addElement('select', Group::PROPERTY_PARENT_ID, Translation::get('Location'), $this->get_groups());
        $this->addRule(
            Group::PROPERTY_PARENT_ID, Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES),
            'required'
        );

        // Disk Quota
        $this->addElement('text', Group::PROPERTY_DISK_QUOTA, Translation::get('DiskQuota'), ['size' => '50']);
        $this->addRule(
            Group::PROPERTY_DISK_QUOTA, Translation::get('ThisFieldMustBeNumeric', null, StringUtilities::LIBRARIES),
            'numeric'
        );
        // Database Quota
        $this->addElement(
            'text', Group::PROPERTY_DATABASE_QUOTA, Translation::get('DatabaseQuota'), ['size' => '50']
        );
        $this->addRule(
            Group::PROPERTY_DATABASE_QUOTA,
            Translation::get('ThisFieldMustBeNumeric', null, StringUtilities::LIBRARIES), 'numeric'
        );

        $this->add_html_editor(
            Group::PROPERTY_DESCRIPTION, Translation::get('Description', null, StringUtilities::LIBRARIES), false
        );
    }

    public function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Create', null, StringUtilities::LIBRARIES)
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_editing_form()
    {
        $group = $this->group;
        $parent = $this->parent;

        $this->build_basic_form();

        $this->addElement('hidden', Group::PROPERTY_ID);

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Update', null, StringUtilities::LIBRARIES), null, null,
            new FontAwesomeGlyph('arrow-right')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function create_group(): bool
    {
        $group = $this->group;
        $values = $this->exportValues();

        $group->set_name($values[Group::PROPERTY_NAME]);
        $group->set_description($values[Group::PROPERTY_DESCRIPTION]);
        $group->set_code($values[Group::PROPERTY_CODE]);
        $group->set_parent($values[NestedSet::PROPERTY_PARENT_ID]);
        if ($values[Group::PROPERTY_DATABASE_QUOTA] != '')
        {
            $group->set_database_quota(intval($values[Group::PROPERTY_DATABASE_QUOTA]));
        }
        if ($values[Group::PROPERTY_DISK_QUOTA] != '')
        {
            $group->set_disk_quota(intval($values[Group::PROPERTY_DISK_QUOTA]));
        }

        return $this->getGroupService()->createGroup($group);
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

    /**
     * Sets default values.
     *
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = [], $filter = null)
    {
        $group = $this->group;
        $defaults[Group::PROPERTY_ID] = $group->get_id();
        $defaults[Group::PROPERTY_PARENT_ID] = $group->get_parent_id();
        $defaults[Group::PROPERTY_NAME] = $group->get_name();
        $defaults[Group::PROPERTY_CODE] = $group->get_code();
        $defaults[Group::PROPERTY_DESCRIPTION] = $group->get_description();
        $defaults[Group::PROPERTY_DATABASE_QUOTA] = $group->get_database_quota();
        $defaults[Group::PROPERTY_DISK_QUOTA] = $group->get_disk_quota();
        parent::setDefaults($defaults);
    }

    /**
     * @throws \QuickformException
     */
    public function update_group(): bool
    {
        $group = $this->group;
        $values = $this->exportValues();

        $group->set_name($values[Group::PROPERTY_NAME]);
        $group->set_description($values[Group::PROPERTY_DESCRIPTION]);
        $group->set_code($values[Group::PROPERTY_CODE]);
        $group->set_database_quota(intval($values[Group::PROPERTY_DATABASE_QUOTA]));
        $group->set_disk_quota(intval($values[Group::PROPERTY_DISK_QUOTA]));

        if (!$this->getGroupService()->updateGroup($group))
        {
            return false;
        }

        $newParentGroupIdentifier = $values[NestedSet::PROPERTY_PARENT_ID];

        if ($group->get_parent_id() != $newParentGroupIdentifier)
        {
            return $this->getGroupService()->moveGroup($group, $newParentGroupIdentifier);
        }

        return true;
    }
}
