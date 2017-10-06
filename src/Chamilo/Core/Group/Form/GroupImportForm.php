<?php

namespace Chamilo\Core\Group\Form;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use DOMDocument;

/**
 *
 * @package groups.lib.forms
 */
class GroupImportForm extends FormValidator
{

    private $doc;

    private $failed_elements;

    /**
     * Creates a new GroupImportForm Used to import groups from a file
     */
    public function __construct($action)
    {
        parent::__construct('group_import', 'post', $action);

        $this->failed_elements = array();
        $this->build_importing_form();
    }

    public function build_importing_form()
    {
        $this->addElement('file', 'file', Translation::get('FileName'));
        $allowed_upload_types = array('xml');
        $this->addRule('file', Translation::get('OnlyXMLAllowed'), 'filetype', $allowed_upload_types);

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Import'),
            null,
            null,
            'import'
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function import_groups()
    {
        $values = $this->exportValues();
        $groups = $this->parse_file($_FILES['file']['tmp_name']);

        foreach ($groups as $group)
        {
            $this->validate_group($group);
        }

        if (count($this->failed_elements) > 0)
        {
            return false;
        }

        $this->process_groups($groups);

        if (count($this->failed_elements) > 0)
        {
            return false;
        }

        return true;
    }

    public function parse_file($file)
    {
        $this->doc = new DOMDocument();
        $this->doc->load($file);
        $group_root = $this->doc->getElementsByTagname('groups')->item(0);

        $group_nodes = $group_root->childNodes;
        foreach ($group_nodes as $node)
        {
            if ($node->nodeName == "#text")
            {
                continue;
            }

            $groups[] = $this->parse_group($node);
        }

        return $groups;
    }

    public function parse_group($group)
    {
        $group_array = array();

        if ($group->hasChildNodes())
        {
            $group_array['action'] = $group->getElementsByTagName('action')->item(0)->nodeValue;
            $group_array['name'] = $group->getElementsByTagName('name')->item(0)->nodeValue;
            $group_array['description'] = $group->getElementsByTagName('description')->item(0)->nodeValue;
            $group_array['code'] = $group->getElementsByTagName('code')->item(0)->nodeValue;
            $children = $group->getElementsByTagName('children')->item(0);

            $group_nodes = $children->childNodes;
            foreach ($group_nodes as $node)
            {
                if ($node->nodeName == "#text")
                {
                    continue;
                }

                $group_array['children'][] = $this->parse_group($node);
            }
        }

        return $group_array;
    }

    public function validate_group($group)
    {
        // 1. Check if action is valid
        $action = strtoupper($group['action']);
        if ($action != 'A' && $action != 'U' && $action != 'D')
        {
            $this->failed_elements[] = Translation::get('Invalid', null, Utilities::COMMON_LIBRARIES) . ': ' .
                $this->display_group($group);

            return $this->validate_children($group['children']);
        }

        // 2. Check if name & code is filled in
        if (!$group['name'] || $group['name'] == '' || !$group['code'] || $group['code'] == '')
        {
            $this->failed_elements[] = Translation::get('Invalid', null, Utilities::COMMON_LIBRARIES) . ': ' .
                $this->display_group($group);

            return $this->validate_children($group['children']);
        }

        // 3. Check if action is valid
        if (($action == 'A' && $this->group_code_exists($group['code'])) ||
            ($action != 'A' && !$this->group_code_exists($group['code'])))
        {
            $this->failed_elements[] = Translation::get('Invalid', null, Utilities::COMMON_LIBRARIES) . ': ' .
                $this->display_group($group);

            return $this->validate_children($group['children']);
        }

        return $this->validate_children($group['children']);
    }

    public function validate_children($children)
    {
        foreach ($children as $child)
        {
            $this->validate_group($child);
        }
    }

    public function process_groups($groups, $parent_group = 1)
    {
        foreach ($groups as $gr)
        {
            $action = strtoupper($gr['action']);

            switch ($action)
            {
                case 'A' :
                    $group = $this->create_group($gr, $parent_group);
                    break;
                case 'U' :
                    $group = $this->update_group($gr, $parent_group);
                    break;
                case 'D' :
                    $group = $this->delete_group($gr);
                    break;
            }

            if (!$group)
            {
                $this->failed_elements[] = Translation::get('Failed') . ': ' . $this->display_group($group);

                return;
            }

            $this->process_groups($gr['children'], $group->get_id());
        }
    }

    public function display_group($group)
    {
        return $group['code'] . ' - ' . $group['name'];
    }

    public function create_group($data, $parent_group)
    {
        $group = new Group();
        $group->set_name($data['name']);
        $group->set_description($data['description']);
        $group->set_code($data['code']);
        $group->set_parent($parent_group);

        if ($group->create())
        {
            return $group;
        }
    }

    public function update_group($data, $parent_group)
    {
        $group = $this->get_group($data['code']);
        $group->set_name($data['name']);
        $group->set_description($data['description']);
        $succes = $group->update();

        if ($group->get_parent() != $parent_group)
        {
            $succes &= $group->move($parent_group);
        }

        if ($succes)
        {
            return $group;
        }
    }

    public function delete_group($data)
    {
        $group = $this->get_group($data['code']);

        // Group is already deleted by parent deletion
        if (!$group)
        {
            return false;
        }

        if ($group->delete())
        {
            return $group;
        }
    }

    public function get_group($code)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_CODE),
            new StaticConditionVariable($code)
        );

        $groups = DataManager::retrieves(Group::class_name(), new DataClassRetrievesParameters($condition));

        return $groups->next_result();
    }

    public function group_code_exists($code)
    {
        return !is_null($this->get_group($code));
    }

    public function get_failed_elements()
    {
        return implode("<br />", $this->failed_elements);
    }
}
