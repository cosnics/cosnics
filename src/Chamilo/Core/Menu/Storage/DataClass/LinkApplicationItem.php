<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LinkApplicationItem extends Item
{
    const PROPERTY_SECTION = 'section';
    const PROPERTY_URL = 'url';
    const PROPERTY_TARGET = 'target';
    const TARGET_BLANK = 0;
    const TARGET_SELF = 1;
    const TARGET_PARENT = 2;
    const TARGET_TOP = 3;

    public function __construct($default_properties = array(), $additional_properties = null)
    {
        parent::__construct($default_properties, $additional_properties);
        $this->set_type(__CLASS__);
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name());
    }

    public function get_section()
    {
        return $this->get_additional_property(self::PROPERTY_SECTION);
    }

    public function set_section($section)
    {
        return $this->set_additional_property(self::PROPERTY_SECTION, $section);
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_SECTION, self::PROPERTY_URL, self::PROPERTY_TARGET);
    }

    public function get_url()
    {
        return $this->get_additional_property(self::PROPERTY_URL);
    }

    public function set_url($url)
    {
        return $this->set_additional_property(self::PROPERTY_URL, $url);
    }

    public function get_target()
    {
        return $this->get_additional_property(self::PROPERTY_TARGET);
    }

    public function set_target($target)
    {
        return $this->set_additional_property(self::PROPERTY_TARGET, $target);
    }

    /**
     *
     * @return string
     */
    public function get_target_string()
    {
        return self::target_string($this->get_target());
    }

    /**
     *
     * @return string
     */
    public static function target_string($target)
    {
        switch ($target)
        {
            case self::TARGET_BLANK :
                return '_blank';
                break;
            case self::TARGET_SELF :
                return '_self';
                break;
            case self::TARGET_PARENT :
                return '_parent';
                break;
            case self::TARGET_TOP :
                return '_top';
                break;
        }
    }

    public function get_target_types($types_only = false)
    {
        $types = array();
        
        $types[self::TARGET_BLANK] = self::target_string(self::TARGET_BLANK);
        $types[self::TARGET_SELF] = self::target_string(self::TARGET_SELF);
        $types[self::TARGET_PARENT] = self::target_string(self::TARGET_PARENT);
        $types[self::TARGET_TOP] = self::target_string(self::TARGET_TOP);
        
        return ($types_only ? array_keys($types) : $types);
    }
}
