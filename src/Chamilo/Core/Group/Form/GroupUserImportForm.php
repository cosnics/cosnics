<?php
namespace Chamilo\Core\Group\Form;

use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\File\Import;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Group\Form
 * @author  vanpouckesven
 */
class GroupUserImportForm extends FormValidator
{

    private $doc;

    private $failed_elements;

    /**
     * Creates a new GroupUserImportForm Used to import group users from a file
     */
    public function __construct($action)
    {
        parent::__construct('group_user_import', self::FORM_METHOD_POST, $action);

        $this->failed_elements = [];
        $this->build_importing_form();
    }

    public function build_importing_form()
    {
        $this->addElement('file', 'file', Translation::get('FileName'));
        $allowed_upload_types = ['csv'];
        $this->addRule('file', Translation::get('OnlyCSVAllowed'), 'filetype', $allowed_upload_types);

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Import', null, StringUtilities::LIBRARIES), null, null,
            new FontAwesomeGlyph('import')
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function create_group_user($group_user)
    {
        $group_rel_user = new GroupRelUser();
        $group_rel_user->set_group_id($group_user['group_code']->get_id());
        $group_rel_user->set_user_id($group_user['username']->get_id());

        return $group_rel_user->create();
    }

    public function getGroupMembershipService(): GroupMembershipService
    {
        return $this->getService(GroupMembershipService::class);
    }

    public function get_failed_elements()
    {
        return implode('<br />', $this->failed_elements);
    }

    public function import_group_users()
    {
        $values = $this->exportValues();
        $group_users = Import::csv_to_array($_FILES['file']['tmp_name']);

        $validated_groups = [];

        foreach ($group_users as $group_user)
        {
            if ($validated_group = $this->validate_group_user($group_user))
            {
                $validated_groups[] = $validated_group;
            }
            else
            {
                $this->failed_elements[] =
                    Translation::get('Invalid', null, StringUtilities::LIBRARIES) . ': ' . implode(';', $group_user);
            }
        }

        if (count($this->failed_elements) > 0)
        {
            return false;
        }

        $this->process_group_users($validated_groups);

        if (count($this->failed_elements) > 0)
        {
            return false;
        }

        return true;
    }

    public function process_group_users($group_users)
    {
        foreach ($group_users as $group_user)
        {
            $action = strtoupper($group_user['action']);
            switch ($action)
            {
                case 'A' :
                    $succes = $this->create_group_user($group_user);
                    break;
                case 'D' :
                    $succes = $group_user['group_user']->delete();
                    break;
            }

            if (!$succes)
            {
                $this->failed_elements[] = Translation::get('Failed') . ': ' . implode(';', $group_user);
            }
        }
    }

    public function retrieve_group($group_code)
    {
        return $this->getGroupService()->findGroupByCode($group_code);
    }

    public function retrieve_group_user($group_id, $user_id)
    {
        return $this->getGroupMembershipService()->getGroupUserRelationByGroupCodeAndUser($group_id, $user_id);
    }

    public function retrieve_user($username)
    {
        return $this->getUserService()->findUserByUsername($username);
    }

    public function validate_group_user($group_user)
    {
        // 1. Check if action is valid
        $action = strtoupper($group_user['action']);
        if ($action != 'A' && $action != 'D')
        {
            return false;
        }

        // 2. Check if name & code is filled in
        if (!$group_user['group_code'] || $group_user['group_code'] == '' || !$group_user['username'] ||
            $group_user['username'] == '')
        {
            return false;
        }

        $group_user['group_code'] = $this->retrieve_group($group_user['group_code']);

        // 3. Check if group exists
        if (!$group_user['group_code'])
        {
            return false;
        }

        $group_user['username'] = $this->retrieve_user($group_user['username']);

        // 4. Check if user exists
        if (!$group_user['username'])
        {
            return false;
        }

        $group_user['group_user'] = $this->retrieve_group_user(
            $group_user['group_code']->get_id(), $group_user['username']->get_id()
        );

        // 5. Check if groupuser exist with delete and if it doesn't exist yet with create
        if (($action == 'A' && $group_user['group_user']) || ($action == 'D' && !$group_user['group_user']))
        {
            return false;
        }

        return $group_user;
    }
}
