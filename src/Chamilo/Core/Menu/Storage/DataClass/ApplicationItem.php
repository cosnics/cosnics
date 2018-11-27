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

    /**
     * @param string[] $defaultProperties
     * @param string[] $additionalProperties
     *
     * @throws \Exception
     */
    public function __construct($defaultProperties = array(), $additionalProperties = null)
    {
        parent::__construct($defaultProperties, $additionalProperties);
        $this->setType(__CLASS__);
    }

    /**
     * @return string
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name());
    }

    /**
     * @return integer
     * @deprecated Use ApplicationItem::getUseTranslation() now
     */
    public function get_use_translation()
    {
        return $this->getUseTranslation();
    }

    /**
     * @param integer $useTranslation
     *
     * @deprecated Use ApplicationItem::setUseTranslation() now
     */
    public function set_use_translation($useTranslation = 0)
    {
        $this->setUseTranslation($useTranslation);
    }

    /**
     * @return integer
     */
    public function getUseTranslation()
    {
        return $this->get_additional_property(self::PROPERTY_USE_TRANSLATION);
    }

    /**
     * @param integer $useTranslation
     */
    public function setUseTranslation($useTranslation = 0)
    {
        $this->set_additional_property(self::PROPERTY_USE_TRANSLATION, $useTranslation);
    }

    /**
     * @return string
     * @deprecated Use ApplicationItem::getApplication() now
     */
    public function get_application()
    {
        return $this->getApplication();
    }

    /**
     * @param string $application
     *
     * @deprecated Use ApplicationItem::setApplication() now
     */
    public function set_application($application)
    {
        return $this->setApplication($application);
    }

    /**
     * @return string
     */
    public function getApplication()
    {
        return $this->get_additional_property(self::PROPERTY_APPLICATION);
    }

    /**
     * @param string $application
     */
    public function setApplication($application)
    {
        return $this->set_additional_property(self::PROPERTY_APPLICATION, $application);
    }

    /**
     * @return string
     */
    public function getComponent()
    {
        return $this->get_additional_property(self::PROPERTY_COMPONENT);
    }

    /**
     * @param string $component
     */
    public function setComponent($component)
    {
        $this->set_additional_property(self::PROPERTY_COMPONENT, $component);
    }

    /**
     * @return string
     */
    public function getExtraParameters()
    {
        return $this->get_additional_property(self::PROPERTY_EXTRA_PARAMETERS);
    }

    /**
     * @param string $extraParameters
     */
    public function setExtraParameters($extraParameters)
    {
        $this->set_additional_property(self::PROPERTY_EXTRA_PARAMETERS, $extraParameters);
    }

    /**
     * @return string[]
     */
    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_USE_TRANSLATION, self::PROPERTY_APPLICATION, self::PROPERTY_COMPONENT,
            self::PROPERTY_EXTRA_PARAMETERS
        );
    }
}
