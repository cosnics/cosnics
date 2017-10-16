<?php
namespace Chamilo\Core\Group\Package;

use Chamilo\Core\Group\Storage\DataClass\Group;

/**
 *
 * @package group.install
 */
/**
 * This installer can be used to create the storage structure for the group application.
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Additional installation steps.
     */
    public function extra()
    {
        if (! $this->create_root_group())
        {
            return false;
        }

        return true;
    }

    public function create_root_group()
    {
        $values = $this->get_form_values();

        $group = new Group();
        $group->set_name($values['organization_name']);
        $group->set_parent(0);
        $group->set_code(strtolower($values['organization_name']));
        $group->create();

        return true;
    }
}
