<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LanguageItem extends Item
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ISOCODE = 'isocode';
    public const PROPERTY_LANGUAGE = 'language';

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
        return [self::PROPERTY_LANGUAGE];
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph()
    {
        return new FontAwesomeGlyph('language', [], null, 'fas');
    }

    /**
     * @return string
     */
    public function getIsocode()
    {
        return $this->getAdditionalProperty(self::PROPERTY_ISOCODE);
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->getAdditionalProperty(self::PROPERTY_LANGUAGE);
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
     * @param string $currentUrl
     */
    public function setCurrentUrl(string $currentUrl)
    {
        $this->currentUrl = $currentUrl;
    }

    /**
     * @param string $isocode
     */
    public function setIsocode($isocode)
    {
        return $this->setAdditionalProperty(self::PROPERTY_ISOCODE, $isocode);
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        return $this->setAdditionalProperty(self::PROPERTY_LANGUAGE, $language);
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
}
