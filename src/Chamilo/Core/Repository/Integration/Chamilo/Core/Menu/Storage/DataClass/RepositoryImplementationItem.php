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
class RepositoryImplementationItem extends Item
{
    const PROPERTY_IMPLEMENTATION = 'implementation';
    const PROPERTY_INSTANCE_ID = 'instance_id';
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

    public function get_implementation()
    {
        return $this->get_additional_property(self :: PROPERTY_IMPLEMENTATION);
    }

    public function set_implementation($implementation)
    {
        return $this->set_additional_property(self :: PROPERTY_IMPLEMENTATION, $implementation);
    }

    public function get_instance_id()
    {
        return $this->get_additional_property(self :: PROPERTY_INSTANCE_ID);
    }

    public function set_instance_id($instance_id)
    {
        return $this->set_additional_property(self :: PROPERTY_INSTANCE_ID, $instance_id);
    }

    public function get_name()
    {
        return $this->get_additional_property(self :: PROPERTY_NAME);
    }

    public function set_name($name)
    {
        return $this->set_additional_property(self :: PROPERTY_NAME, $name);
    }

    public static function get_additional_property_names()
    {
        return array(self :: PROPERTY_IMPLEMENTATION, self :: PROPERTY_INSTANCE_ID, self :: PROPERTY_NAME);
    }
}
