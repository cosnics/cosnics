<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 *
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LanguageItem extends Item
{
    const PROPERTY_LANGUAGE = 'language';
    const PROPERTY_ISOCODE = 'isocode';

    /**
     * @var string
     */
    private $currentUrl;

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
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class);
    }

    /**
     * @return string
     * @deprecated Use LanguageItem::getLanguage() now
     */
    public function get_language()
    {
        return $this->getLanguage();
    }

    /**
     * @param string $language
     *
     * @deprecated Use LanguageItem::setLanguage() now
     */
    public function set_language($language)
    {
        return $this->setLanguage($language);
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->get_additional_property(self::PROPERTY_LANGUAGE);
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        return $this->set_additional_property(self::PROPERTY_LANGUAGE, $language);
    }

    /**
     * @return string
     */
    public function getIsocode()
    {
        return $this->get_additional_property(self::PROPERTY_ISOCODE);
    }

    /**
     * @param string $isocode
     */
    public function setIsocode($isocode)
    {
        return $this->set_additional_property(self::PROPERTY_ISOCODE, $isocode);
    }

    /**
     * @return string[]
     */
    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_LANGUAGE);
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

    /**
     * @param string $currentUrl
     */
    public function setCurrentUrl(string $currentUrl)
    {
        $this->currentUrl = $currentUrl;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph()
    {
        return new FontAwesomeGlyph('language', array(), null, 'fas');
    }
}
