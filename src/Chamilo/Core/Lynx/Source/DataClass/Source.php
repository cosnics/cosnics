<?php
namespace Chamilo\Core\Lynx\Source\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package core.lynx.source
 * @author Hans De Bisschop
 */
class Source extends DataClass
{

    /**
     * Source properties
     */
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_URI = 'uri';
    const PROPERTY_STATUS = 'status';
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_URI, self :: PROPERTY_STATUS));
    }

    /**
     * Returns the name of this Source.
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this Source.
     *
     * @param string
     */
    public function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the description of this Source.
     *
     * @return string
     */
    public function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this Source.
     *
     * @param string
     */
    public function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the uri of this Source.
     *
     * @return string
     */
    public function get_uri()
    {
        return $this->get_default_property(self :: PROPERTY_URI);
    }

    /**
     * Sets the uri of this Source.
     *
     * @param string uri
     */
    public function set_uri($uri)
    {
        $this->set_default_property(self :: PROPERTY_URI, $uri);
    }

    /**
     * Returns the status of this Source.
     *
     * @return int
     */
    public function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Sets the status of this Source.
     *
     * @param int status
     */
    public function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }
}
