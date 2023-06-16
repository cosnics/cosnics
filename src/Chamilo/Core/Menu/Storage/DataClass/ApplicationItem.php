<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ApplicationItem extends Item
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_APPLICATION = 'application';
    public const PROPERTY_COMPONENT = 'component';
    public const PROPERTY_EXTRA_PARAMETERS = 'extra_parameters';
    public const PROPERTY_USE_TRANSLATION = 'use_translation';

    /**
     * @param string[] $defaultProperties
     * @param string[] $additionalProperties
     *
     * @throws \Exception
     */
    public function __construct($defaultProperties = [], $additionalProperties = [])
    {
        parent::__construct($defaultProperties, $additionalProperties);
        $this->setType(__CLASS__);
    }

    /**
     * @return string[]
     */
    public static function getAdditionalPropertyNames(): array
    {
        return [
            self::PROPERTY_USE_TRANSLATION,
            self::PROPERTY_APPLICATION,
            self::PROPERTY_COMPONENT,
            self::PROPERTY_EXTRA_PARAMETERS
        ];
    }

    /**
     * @return string
     */
    public function getApplication()
    {
        return $this->getAdditionalProperty(self::PROPERTY_APPLICATION);
    }

    /**
     * @return string
     */
    public function getComponent()
    {
        return $this->getAdditionalProperty(self::PROPERTY_COMPONENT);
    }

    /**
     * @return string
     */
    public function getExtraParameters()
    {
        return $this->getAdditionalProperty(self::PROPERTY_EXTRA_PARAMETERS);
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('desktop', [], null, 'fas');
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'menu_application_item';
    }

    /**
     * @return int
     */
    public function getUseTranslation()
    {
        return $this->getAdditionalProperty(self::PROPERTY_USE_TRANSLATION);
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
     * @return int
     * @deprecated Use ApplicationItem::getUseTranslation() now
     */
    public function get_use_translation()
    {
        return $this->getUseTranslation();
    }

    /**
     * @param string $application
     */
    public function setApplication($application)
    {
        return $this->setAdditionalProperty(self::PROPERTY_APPLICATION, $application);
    }

    /**
     * @param string $component
     */
    public function setComponent($component)
    {
        $this->setAdditionalProperty(self::PROPERTY_COMPONENT, $component);
    }

    /**
     * @param string $extraParameters
     */
    public function setExtraParameters($extraParameters)
    {
        $this->setAdditionalProperty(self::PROPERTY_EXTRA_PARAMETERS, $extraParameters);
    }

    /**
     * @param int $useTranslation
     */
    public function setUseTranslation($useTranslation = 0)
    {
        $this->setAdditionalProperty(self::PROPERTY_USE_TRANSLATION, $useTranslation);
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
     * @param int $useTranslation
     *
     * @deprecated Use ApplicationItem::setUseTranslation() now
     */
    public function set_use_translation($useTranslation = 0)
    {
        $this->setUseTranslation($useTranslation);
    }
}
