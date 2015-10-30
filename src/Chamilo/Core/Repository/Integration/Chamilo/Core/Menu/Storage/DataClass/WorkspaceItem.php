<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceItem extends Item
{
    const PROPERTY_WORKSPACE_ID = 'workspace_id';
    const PROPERTY_NAME = 'name';

    public function __construct($default_properties = array(), $additional_properties = null)
    {
        parent :: __construct($default_properties, $additional_properties);
        $this->set_type(__CLASS__);
    }

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name());
    }

    public function getWorkspaceId()
    {
        return $this->get_additional_property(self :: PROPERTY_WORKSPACE_ID);
    }

    public function setWorkspaceId($workspace_id)
    {
        return $this->set_additional_property(self :: PROPERTY_WORKSPACE_ID, $workspace_id);
    }

    public function getName()
    {
        return $this->get_additional_property(self :: PROPERTY_NAME);
    }

    public function setName($name)
    {
        return $this->set_additional_property(self :: PROPERTY_NAME, $name);
    }

    public static function get_additional_property_names()
    {
        return array(self :: PROPERTY_WORKSPACE_ID, self :: PROPERTY_NAME);
    }
}
