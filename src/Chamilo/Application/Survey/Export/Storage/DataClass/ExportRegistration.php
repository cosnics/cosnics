<?php
namespace Chamilo\Application\Survey\Export\Storage\DataClass;

use Chamilo\Application\Survey\Export\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class ExportRegistration extends DataClass
{
    const TABLE_NAME = 'export_registration';
    const PROPERTY_TEMPLATE = 'template';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';

    function get_template()
    {
        return $this->get_default_property(self :: PROPERTY_TEMPLATE);
    }

    function set_template($template)
    {
        $this->set_default_property(self :: PROPERTY_TEMPLATE, $template);
    }

    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_TEMPLATE, self :: PROPERTY_TYPE, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return DataManager :: getInstance();
    }
}
?>