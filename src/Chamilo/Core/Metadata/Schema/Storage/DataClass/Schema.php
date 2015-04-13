<?php
namespace Chamilo\Core\Metadata\Schema\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class describes a metadata schema
 *
 * @package Ehb\Core\Metadata\Schema\Storage\DataClass
 * @author Jens Vanderheyden
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Schema extends DataClass
{
    use \Chamilo\Core\Metadata\Traits\EntityTranslationTrait;

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_NAMESPACE = 'namespace';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_URL = 'url';
    const PROPERTY_FIXED = 'fixed';

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */

    /**
     * Get the default properties
     *
     * @param string[] $extended_property_names
     *
     * @return string[] The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_NAMESPACE;
        $extended_property_names[] = self :: PROPERTY_NAME;
        $extended_property_names[] = self :: PROPERTY_DESCRIPTION;
        $extended_property_names[] = self :: PROPERTY_URL;
        $extended_property_names[] = self :: PROPERTY_FIXED;

        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the namespace
     *
     * @return string
     */
    public function get_namespace()
    {
        return $this->get_default_property(self :: PROPERTY_NAMESPACE);
    }

    /**
     * Sets the namespace
     *
     * @param string $namespace
     */
    public function set_namespace($namespace)
    {
        $this->set_default_property(self :: PROPERTY_NAMESPACE, $namespace);
    }

    /**
     * Returns the name
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the description
     *
     * @return string
     */
    public function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description
     *
     * @param string $description
     */
    public function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the url
     *
     * @return string
     */
    public function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    /**
     * Sets the url
     *
     * @param string $url
     */
    public function set_url($url)
    {
        $this->set_default_property(self :: PROPERTY_URL, $url);
    }

    /**
     * Returns whether or not this element is fixed
     *
     * @return string
     */
    public function is_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_FIXED);
    }

    /**
     * Sets whether or not the element is fixed
     *
     * @param string $fixed
     */
    public function set_fixed($fixed)
    {
        $this->set_default_property(self :: PROPERTY_FIXED, $fixed);
    }
}