<?php
namespace Chamilo\Core\Group\Package;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;

/**
 * @package Chamilo\Core\Group\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    public function createRootGroup(): bool
    {
        $values = $this->get_form_values();

        $group = new Group();
        $group->set_name($values['organization_name']);
        $group->setParentId('0');
        $group->set_code(strtolower($values['organization_name']));

        return $this->getGroupService()->createGroup($group);
    }

    public function extra(): bool
    {
        if (!$this->createRootGroup())
        {
            return false;
        }

        return true;
    }
}
