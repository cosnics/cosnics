<?php
namespace Chamilo\Configuration\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package common.libraries
 * @author  Hans De Bisschop
 * @author  Magali Gillard
 */
class Language extends DataClass
{
    public const CONTEXT = 'Chamilo\Configuration';

    public const PROPERTY_AVAILABLE = 'available';
    public const PROPERTY_ENGLISH_NAME = 'english_name';
    public const PROPERTY_FAMILY = 'family';
    public const PROPERTY_ISOCODE = 'isocode';
    public const PROPERTY_ORIGINAL_NAME = 'original_name';

    /**
     * Get the default properties of all languages
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_ORIGINAL_NAME,
                self::PROPERTY_ENGLISH_NAME,
                self::PROPERTY_FAMILY,
                self::PROPERTY_ISOCODE,
                self::PROPERTY_AVAILABLE
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'configuration_language';
    }

    /**
     * Get the availability of the language
     *
     * @return int
     */
    public function get_available()
    {
        return $this->getDefaultProperty(self::PROPERTY_AVAILABLE);
    }

    /**
     * Get the english name of the language
     *
     * @return string
     */
    public function get_english_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENGLISH_NAME);
    }

    /**
     * Get the family of the language
     *
     * @return string
     */
    public function get_family()
    {
        return $this->getDefaultProperty(self::PROPERTY_FAMILY);
    }

    /**
     * Get the ISO 639-1 code of the language
     *
     * @return string
     */
    public function get_isocode()
    {
        return $this->getDefaultProperty(self::PROPERTY_ISOCODE);
    }

    /**
     * Get the native name of the language
     *
     * @return string
     */
    public function get_original_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_ORIGINAL_NAME);
    }

    /**
     * Set the availability of the language
     *
     * @return int
     */
    public function is_available()
    {
        return $this->get_available();
    }

    public function set_available($available)
    {
        $this->setDefaultProperty(self::PROPERTY_AVAILABLE, $available);
    }

    /**
     * Set the english name of the language
     *
     * @param string $original_name
     */
    public function set_english_name($english_name)
    {
        $this->setDefaultProperty(self::PROPERTY_ENGLISH_NAME, $english_name);
    }

    /**
     * Set the family of the language
     *
     * @param string $family
     */
    public function set_family($family)
    {
        $this->setDefaultProperty(self::PROPERTY_FAMILY, $family);
    }

    /**
     * Set the ISO 639-1 code of the language
     *
     * @param string $isocode
     */
    public function set_isocode($isocode)
    {
        $this->setDefaultProperty(self::PROPERTY_ISOCODE, $isocode);
    }

    /**
     * Set the native name of the language
     *
     * @param string $original_name
     */
    public function set_original_name($original_name)
    {
        $this->setDefaultProperty(self::PROPERTY_ORIGINAL_NAME, $original_name);
    }
}
