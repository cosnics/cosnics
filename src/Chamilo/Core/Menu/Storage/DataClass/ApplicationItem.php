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
class ApplicationItem extends Item
{
    const PROPERTY_USE_TRANSLATION = 'use_translation';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_COMPONENT = 'component';
    const PROPERTY_EXTRA_PARAMETERS = 'extra_parameters';

    public function __construct($default_properties = array(), $additional_properties = null)
    {
        parent::__construct($default_properties, $additional_properties);
        $this->set_type(__CLASS__);
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name());
    }

    public function get_use_translation()
    {
        return $this->get_additional_property(self::PROPERTY_USE_TRANSLATION);
    }

    public function set_use_translation($use_translation = 0)
    {
        return $this->set_additional_property(self::PROPERTY_USE_TRANSLATION, $use_translation);
    }

    public function get_application()
    {
        return $this->get_additional_property(self::PROPERTY_APPLICATION);
    }

    public function set_application($application)
    {
        return $this->set_additional_property(self::PROPERTY_APPLICATION, $application);
    }

    public function getComponent()
    {
        return $this->get_additional_property(self::PROPERTY_COMPONENT);
    }

    public function setComponent($component)
    {
        $this->set_additional_property(self::PROPERTY_COMPONENT, $component);
    }

    public function getExtraParameters()
    {
        return $this->get_additional_property(self::PROPERTY_EXTRA_PARAMETERS);
    }

    public function setExtraParameters($extraParameters)
    {
        $this->set_additional_property(self::PROPERTY_EXTRA_PARAMETERS, $extraParameters);
    }

    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_USE_TRANSLATION, 
            self::PROPERTY_APPLICATION, 
            self::PROPERTY_COMPONENT, 
            self::PROPERTY_EXTRA_PARAMETERS);
    }
}
