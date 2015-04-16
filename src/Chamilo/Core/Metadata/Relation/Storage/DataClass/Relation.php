<?php
namespace Chamilo\Core\Metadata\Relation\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class describes a metadata schema
 * 
 * @package Chamilo\Core\Metadata\Relation\Storage\DataClass
 * @author Jens Vanderheyden
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Relation extends DataClass
{
    use \Chamilo\Core\Metadata\Traits\EntityTranslationTrait;
    
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_NAME = 'name';
    const PROPERTY_DISPLAY_NAME = 'display_name';

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
        $extended_property_names[] = self :: PROPERTY_NAME;
        $extended_property_names[] = self :: PROPERTY_DISPLAY_NAME;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
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
     * Returns the display_name
     * 
     * @return string
     */
    public function get_display_name()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_NAME);
    }

    /**
     * Sets the display_name
     * 
     * @param string $display_name
     */
    public function set_display_name($display_name)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_NAME, $display_name);
    }
}